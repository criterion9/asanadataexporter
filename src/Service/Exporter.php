<?php

/**
 * Description of Exporter
 * 
 * PHP version 8
 * 
 * * * License * * * 
 * Copyright (C) 2024 Andrew Wallace <criterion9@proton.me>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301  USA
 * * * End License * * * 
 * 
 * @category  CategoryName
 * @package   PackageName
 * @author    Andrew Wallace <criterion9@proton.me>
 * @copyright 2024 Andrew Wallace <criterion9@proton.me>
 * @license   LGPL2.1
 * @version   GIT: $ID$
 * @link      https://github.com/criterion9/asanadataexporter
 */

namespace Criterion9\AsanaDataExporter\Service;

use Asana\Client;
use Exception;
use IteratorIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use stdClass;
use ZipArchive;

/**
 * Description of Exporter
 *
 * @category  CategoryName
 * @package   PackageName
 * @author    Andrew Wallace <criterion9@proton.me>
 * @copyright 2024 Andrew Wallace <criterion9@proton.me>
 * @license   LGPL2.1
 * @version   Release: @package_version@
 * @link      
 * @since     Class available since Release 0.0.0
 */
class Exporter {

    const __SLOW = 1, __NORMAL = 3, __FAST = 5;
    private $access_token, $me;
    protected $config, $client, $attachments, $lastRest, $speed, $statusHeaders = [
                'html_text', 'resource_type', 'resource_subtype', 'title', 'author', 'created_at', 'modified_at'
    ];

    public function __construct($config = []) {
        $this->config = $config;
        $this->speed = self::__NORMAL;
    }
    
    public function setSpeed($speed = self::__NORMAL){
        if(in_array($speed, [self::__SLOW,self::__NORMAL, self::__FAST])){
            $this->speed = $speed;
        }
    }
    
    private function restTime() {
        if(($this->lastRest - time()) >= 1*max(($this->speed/2),1)){
            $toRest = 1;
        } else {
            $toRest = max(round(rand(0, (90/$this->speed)) / 100),0);
        }
        if($toRest){
            sleep(1);
            $this->lastRest = time();
        }
    }

    public function setToken(string $token): void {
        $this->access_token = $token;
    }

    public function exportAll(string $working_directory, array $settings = ['include_subtasks' => true, 'include_attachments' => true]) {
        throw new \Exception('The exportAll method has not been implemented');
        if (!is_dir($working_directory)) {
            throw new Exception('Working directory doesn\'t exist.');
            return false;
        }
        if (!is_writable($working_directory)) {
            throw new Exception('Working directory is not writable.');
        }

        $projects = isset($settings['projects']) ? $settings['projects'] : false;
        $teams = isset($settings['teams']) ? $settings['teams'] : false;
        $workspace = isset($settings['workspace']) ? $settings['workspace'] : false;
        $include_subtasks = isset($settings['include_subtasks']) ? $settings['include_subtasks'] : $this->config['output']['include_subtasks'];
        $include_attachments = isset($settings['include_attachments']) ? $settings['include_attachments'] : $this->config['output']['include_attachments'];
        $compress_output = isset($settings['compress_output']) ? $settings['compress_output'] : $this->config['output']['compress'];

        if (!$projects && !$teams && !$workspace) {
            $workspaces = $this->getWorkspaces();
            if (count($workspace) == 1) {
                $workspace = $workspaces[0];
            } else {
                //need to limit to at least the workspace
                return false;
            }
        }
    }

    public function exportProjectTasks(string $working_directory, array $settings = ['include_subtasks' => true]) {
        if (!is_dir($working_directory)) {
            throw new Exception('Working directory doesn\'t exist.');
        }
        if (!is_writable($working_directory)) {
            throw new Exception('Working directory is not writable.');
        }
        $projects = isset($settings['projects']) ? $settings['projects'] : false;
        if (!$projects) {
            throw new Exception('Must provide an array of project(s).');
        }
        $include_subtasks = isset($settings['include_subtasks']) ? $settings['include_subtasks'] : $this->config['output']['include_subtasks'];
        $client = $this->getClient();
        $filter = [];
        if (isset($settings['completed_since'])) {
            $filter['completed_since'] = $settings['completed_since'];
        }
        if (isset($settings['modified_since'])) {
            $filter['modified_since'] = $settings['modified_since'];
        }
        $acceptable_subtypes = ['default_task'];
        $tasks = [];
        foreach ($projects as $project) {
            $filter['project'] = $project->gid;
            if (isset($settings['progress'])) {
                $settings['progress']->clear();
            }
            foreach ($client->tasks->findAll($filter, ['page_size' => 100]) as $t) {
                $this->restTime();
                $result = $this->get_task($t->gid, $settings);
                $tasks[is_object($result['task']) ? $result['task']->gid : $result['task']['gid']] = $result['task'];
                if (isset($settings['progress'])) {
                    $settings['progress']->advance();
                }
            }
            if (isset($settings['progress'])) {
                $settings['progress']->finish();
            }
        }
        $this->writeTasks($working_directory, $tasks, $settings);
        return $tasks;
    }

    private function get_task($gid, $settings) {
        $status = "OK";
        $comments = [];
        $subtasks = [];

        $client = $this->getClient();
        $task_data = $client->tasks->getTask($gid);
        $this->restTime();
        $include_subtasks = isset($settings['include_subtasks']) ? $settings['include_subtasks'] : $this->config['output']['include_subtasks'];
        $include_attachments = isset($settings['include_attachments']) ? $settings['include_attachments'] : $this->config['output']['include_attachments'];
        foreach ($client->stories->getStoriesForTask($gid) as $story) {
            $this->restTime();
            if ($story->type == 'comment') {
                $comments[] = [
                    'created_at' => $story->created_at,
                    'text' => $story->text
                ];
            }
        }

        if ($include_attachments) {
            foreach ($client->attachments->getAttachmentsForObject(['parent' => $gid]) as $attachment) {
                $this->restTime();
                $attachment_data = $client->attachments->findById($attachment->gid);
                $tmp = [
                    'created_at' => $attachment_data->created_at,
                    'text' => $attachment_data->name,
                    'url' => $attachment_data->view_url
                ];
                if ($include_attachments) {
                    $this->attachments[] = $tmp;
                }
                $comments[] = $tmp;
            }
        }

        usort($comments, function ($a, $b) {
            return ($a['created_at'] < $b['created_at']) ? -1 : 1;
        });
        if ($include_subtasks) {
            foreach ($client->tasks->getSubtasksForTask($gid) as $subtask) {
                $this->restTime();
                $result = $this->get_task($subtask->gid, $settings);
                if ($result['status'] == 'OK') {
                    $subtasks[] = $result['task'];
                }
            }
        }

        usort($subtasks, function ($a, $b) {
            return ($a['created_at'] < $b['created_at']) ? -1 : 1;
        });

        $res = ['status' => $status,
            'task' => [
                'gid' => $task_data->gid,
                'assignee' => !empty($task_data->assignee->name) ? $task_data->assignee->name : 'null',
                'created_at' => $task_data->created_at,
                'completed_at' => $task_data->completed ? $this->format_timestamp($task_data->completed_at) : '',
                'name' => $task_data->name,
                'custom_fields' => $task_data->custom_fields,
                'notes' => $task_data->notes,
                'comments' => $comments,
                'subtasks' => $subtasks
        ]];

        $custom_fields = [];
        foreach ($task_data->custom_fields as $custField) {
            $res['task'][$custField->name] = $custField->display_value;
        }

        return $res;
    }

    private function format_timestamp($str) {
        return strftime('%m/%d/%y %r %Z', strtotime($str));
    }

    public function fetchAttachments(string $working_directory, array $list = [], $progress = null) {
        $attachment_directory = $working_directory . DIRECTORY_SEPARATOR . 'attachments';
        if (!is_dir($attachment_directory)) {
            mkdir($attachment_directory);
        }
        if (!is_dir($attachment_directory) || !is_writable($attachment_directory)) {
            throw new \Exception('Unable to locate or write to the attachment directory.');
        }
        $list = ($list != []) ? $list : (isset($this->attachments) ? $this->attachments : []);
        if ($list != []) {
            if (!is_null($progress)) {
                $progress->clear();
            }
            foreach ($list as $l) {
                if (!file_exists($attachment_directory . DIRECTORY_SEPARATOR . $l['text'])) {
                    $file = $l['url'];
                    if (function_exists('curl_version')) {
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, $file);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                        $content = curl_exec($curl);
                        curl_close($curl);
                    } else if (file_get_contents(__FILE__) && ini_get('allow_url_fopen')) {
                        $content = file_get_contents($file);
                    } else {
                        throw new \Exception('You have neither cUrl installed nor allow_url_fopen activated. Attachments cannot be downloaded at this time.');
                    }
                    if (!is_null($content)) {
                        file_put_contents($attachment_directory . DIRECTORY_SEPARATOR . $l['text'], $content);
                    }
                }
                $this->restTime();
                $progress->advance();
            }
            if (!is_null($progress)) {
                $progress->finish();
            }
        }
    }

    public function writeTasks(string $working_directory, array $tasks, array $settings = ['compress_output' => false, 'include_json' => true, 'include_csv' => true]) {
        if (!is_dir($working_directory)) {
            throw new Exception('Working directory doesn\'t exist.');
        }
        if (!is_writable($working_directory)) {
            throw new Exception('Working directory is not writable.');
        }
        $include_json = isset($settings['include_json']) ? $settings['include_json'] : $this->config['output']['include_json'];
        if ($include_json) {
            $this->writeTaskJson($working_directory, $tasks);
        }
        $include_csv = isset($settings['include_csv']) ? $settings['include_csv'] : $this->config['output']['include_csv'];
        if ($include_csv) {
            $this->writeTaskCsv($working_directory, $tasks);
        }
    }

    private function writeTaskJson(string $working_directory, array $tasks) {
        file_put_contents($working_directory . DIRECTORY_SEPARATOR . 'tasks.json', json_encode($tasks));
    }

    public function clearAttachmentCache() {
        $this->attachments = [];
    }

    private function writeTaskCsv(string $working_directory, array $tasks) {
        $flattened = $this->flatten($tasks);
        $count_notes = 0;
        $count_comments = 0;
        $headers = [];
        foreach ($flattened as $flat) {
            if (is_array($flat)) {
                $headers = array_merge($headers, array_keys($flat));
            }
        }
        $headers = array_unique($headers);
        sort($headers);
        $fh = fopen($working_directory . DIRECTORY_SEPARATOR . 'tasks.csv', 'w');
        fputcsv($fh, $headers);
        foreach ($flattened as $row) {
            if (is_array($row)) {
                $tmp = [];
                foreach ($headers as $header) {
                    $tmp[$header] = isset($row[$header]) ? $row[$header] : '';
                }
                fputcsv($fh, $tmp);
            } else {
                fwrite($fh, $row);
            }
        }
        fclose($fh);
    }

    public function writeProject(string $working_directory, \stdClass $project, array $project_statuses = [], array $settings = ['compress_output' => false, 'include_json' => true, 'include_csv' => true]) {
        if (!is_dir($working_directory)) {
            throw new Exception('Working directory doesn\'t exist.');
        }
        if (!is_writable($working_directory)) {
            throw new Exception('Working directory is not writable.');
        }
        $include_json = isset($settings['include_json']) ? $settings['include_json'] : $this->config['output']['include_json'];
        if ($include_json) {
            $this->writeProjectJson($working_directory, $project);
        }
        $include_csv = isset($settings['include_csv']) ? $settings['include_csv'] : $this->config['output']['include_csv'];
        if ($include_csv) {
            $this->writeProjectCsv($working_directory, $project);
        }
    }

    private function writeProjectJson(string $working_directory, \stdClass $project) {
        file_put_contents($working_directory . DIRECTORY_SEPARATOR . 'project.json', json_encode($project));
    }

    private function writeProjectCsv(string $working_directory, \stdClass $project) {
        $flattened = $this->flatten([$project]);
        $fh = fopen($working_directory . DIRECTORY_SEPARATOR . 'project.csv', 'w');
        $flattened[0]->team = $flattened[0]->team->name;
        fputcsv($fh, ['gid' => $flattened[0]->gid, 'name' => $flattened[0]->name, 'resource_type' => $flattened[0]->resource_type, 'team' => $flattened[0]->team]);
        fputcsv($fh, []);
        fputcsv($fh, []);
        $headers = null;
        $project_statuses = isset($project->status) ? $project->status : [];
        if ($project_statuses != []) {
            $flattened = $this->flattenToCsv($project_statuses);
            fputcsv($fh, []);
            fputcsv($fh, []);
            fputcsv($fh, $this->statusHeaders);
            foreach ($flattened as $row) {
                if (is_array($row)) {
                    fputcsv($fh, $row);
                } elseif (is_object($row) && $row instanceof \stdClass) {
                    $tmp = [];
                    foreach ($this->statusHeaders as $header) {
                        $tmp[$header] = isset($row->$header) ? $row->$header : '';
                    }
                    fputcsv($fh, $tmp);
                } else {
                    fwrite($fh, $row);
                }
            }
        }
        fclose($fh);
    }

    private function flattenToCsv(array $thing) {
        $res = [];
        foreach ($thing as $key => $val) {
            if ($key == 'custom_fields') {
                continue;
            }
            if (is_array($val)) {
                $subRes = [];
                $i = 0;
                foreach ($val as $subKey => $subVal) {
                    if (is_array($subVal)) {
                        $res[$key . $i] = json_encode($this->flattenToCsv($subVal));
                    } else {
                        $res[$key . $i] = $subVal;
                    }
                    $i++;
                }
            } elseif (is_object($val)) {
                $res[$key] = json_encode($val);
            } else {
                $res[$key] = $val;
            }
        }
        return $res;
    }

    private function flatten(array $thing, $depth = 0) {
        $res = [];
        foreach ($thing as $key => $val) {
            if ($key == 'custom_fields') {
                continue;
            }
            if (!is_array($val)) {
                $res[$key] = $val;
            } elseif (is_array($val) && $depth < 3) {
                $res[$key] = $this->flattenToCsv($val, $depth + 1);
            } else {
                $res[$key] = json_encode($val);
            }
        }
        return $res;
    }

    public function compressOutput(string $directory, string $archivename) {
        $rootPath = realpath(rtrim($directory, '\\/'));
        $zip = new ZipArchive();
        $zip->open($archivename, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($rootPath),
                RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();
        return true;
    }

    public function getWorkspaces() {
        $me = $this->getMe();
        return $me->workspaces;
    }

    public function getProjectStatus(\stdClass $project) {
        $client = $this->getClient();
        $ret = [];
        foreach ($client->statusupdates->getStatusesForObject(['parent' => $project->gid], ['iterator_type' => false, 'page_size' => null, 'opt_fields' => implode(',', $this->statusHeaders)]) as $row) {
            $this->restTime();
            $ret[] = $row;
        }
        return $ret;
    }

    public function getTeamProjects(stdClass $team) {
        $client = $this->getClient();
        return $client->projects->getProjects(['team' => $team->gid], ['iterator_type' => false, 'page_size' => null])->data;
    }

    public function getTeams(stdClass $workspace, $offset = null) {
        $client = $this->getClient();
        if (!is_null($offset)) {
            $res = $client->teams->getTeamsForWorkspace($workspace->gid, ['limit' => 100, 'offset' => $offset]);
        } else {
            $res = $client->teams->getTeamsForWorkspace($workspace->gid, ['limit' => 100]);
        }
        $this->restTime();
        $teams = [];
        if (isset($res->pages->next_page)) {
            $teams = $this->getTeamProjects($workspace, $res->pages->next_page->offset);
        }
        foreach ($res as $team) {
            $teams[] = $team;
        }
        return $teams;
    }

    public function getClient() {
        if (is_null($this->client)) {
            $this->client = Client::accessToken($this->access_token, ['headers' => ['asana-enable' => 'string_ids,new_sections,new_user_task_lists', 'asana-disable' => 'new_goal_memberships']]);
        }
        return $this->client;
    }

    public function getMe() {
        if (is_null($this->me)) {
            $this->me = $this->getClient()->users->getUser('me');
        }
        return $this->me;
    }
}
