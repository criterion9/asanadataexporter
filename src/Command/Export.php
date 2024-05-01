<?php

/**
 * Description of Export
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

namespace Criterion9\AsanaDataExporter\Command;

use Criterion9\AsanaDataExporter\Service\Exporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\ProgressIndicator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputOption;

/**
 * Description of Export
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
class Export extends Command {

    protected $exporter, $config, $initialize, $interact;
    protected $workspace, $team, $project, $outputFolder, $outputsubfolder;
    protected $includeSubtasks, $includeAttachments, $compressEnabled, $currentSection,
            $cleanAfterCompress, $includeStatuses;

    public function __construct(Exporter $exporter, $config) {
        $this->exporter = $exporter;
        $this->config = $config;
        parent::__construct();
    }

    protected function configure(): void {
        $this->setName('asanadataexporter:export');
        $this->setDescription('Exports from Asana for a workspace, project, or other criteria and includes all tasks, sub-tasks, and comments');
        $this->addArgument('token', InputArgument::OPTIONAL, 'Your Asana Personal Access Token (PAT)');
        $this->addArgument('output', InputArgument::OPTIONAL, 'Folder to output the exported data');
        $this->addOption('outputsubfolder', null, InputOption::VALUE_OPTIONAL, 'Working directory folder to output the exported data');
        $this->addOption('workspace', null, InputOption::VALUE_OPTIONAL, 'The workspace to export, automatically selected if you only have access to 1 workspace.');
        $this->addOption('team', null, InputOption::VALUE_OPTIONAL, 'The team to export');
        $this->addOption('project', null, InputOption::VALUE_OPTIONAL, 'The project to export');
        $this->addOption('include_subtasks', null, InputOption::VALUE_OPTIONAL, 'Whether to export subtasks, default is true',true);
        $this->addOption('include_attachments', null, InputOption::VALUE_OPTIONAL, 'Whether to export attachments, default is true',true);
        $this->addOption('include_projectstatus', null, InputOption::VALUE_OPTIONAL, 'Whether to export project statuses, default is true',true);
        $this->addOption('compress_output', null, InputOption::VALUE_OPTIONAL, 'Whether to compress the output, default is true',true);
        $this->addOption('keep_raw_output', null, InputOption::VALUE_OPTIONAL,'Whether to keep the raw output or remove all working files after compression, default is false',false);
        $this->addOption('speed', null, InputOption::VALUE_OPTIONAL,'The speed [slow, normal, fast] to run the export requests, the default is normal', 'normal');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void {
        //$output->write(sprintf("\033\143"));
        $this->currentSection = $output->section();
        $this->currentSection->clear();
        $helper = $this->getHelper('process');
        $process = new Process(['figlet', 'Asana Data Exporter']);
        $output->writeln($helper->run($output, $process));
        $io = new SymfonyStyle($input, $output);
        $io->title('Asana Data Exporter © 2024 Andrew Wallace <criterion9@proton.me>');
        $output->writeln('<href=https://github.com/criterion9/asanadataexporter>View on Github</>');
        $io->newLine();
        $io->section('License');
        $io->block('This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
MA 02110-1301  USA', null, null, '* ');
        $io->newLine();
        sleep(rand(0, 2) + 1);
        $this->compressEnabled = $input->getOption('compress_output');
        if (empty($this->compressEnabled)) {
            $this->compressEnabled = $this->config['output']['compress'];
        }
        $this->cleanAfterCompress = ($input->getOption('keep_raw_output'))?false:true;
        $speed = in_array($input->getOption('speed'),['slow','normal','fast'])?$input->getOption('speed'):'normal';
        $this->exporter->setSpeed($speed);
        $this->initialize = Command::SUCCESS;
        ProgressBar::setFormatDefinition('minimal', '%percent%% %remaining%');
        ProgressBar::setFormatDefinition('minimal_nomax', '%percent%%');
        ProgressBar::setFormatDefinition('verbose_with_message',' %current% [%bar%] %percent:3s%% %elapsed:16s%/%estimated:-16s% %message%');
        ProgressBar::setFormatDefinition('verbose_with_message_nomax',' %current% [%bar%] %percent:3s%% %elapsed:16s% %message%');
    }

    private function checkDirectory(InputInterface $input, SymfonyStyle $io): void {
        // check the output directory
        $this->outputFolder = $input->getArgument('output');
        if (empty($this->outputFolder)) {
            $this->outputFolder = $this->config['output']['defaultlocation'];
        }

        if (!is_dir($this->outputFolder) && !$this->config['output']['createifnotexist']) {
            $io->newLine();
            if (!$io->confirm('Working directory does not exist. Did you want to attempt to create it?', false)) {
                $this->interact = Command::SUCCESS;
                return;
            }
            $this->config['output']['createifnotexist'] = true;
        }
        if (!is_dir($this->outputFolder) && $this->config['output']['createifnotexist']) {
            if (!mkdir($this->outputFolder, 0777, true)) {
                $io->error('Unable to create the output folder location. Check your configuration or permissions and try again.');
                $this->interact = Command::FAILURE;
                return;
            }
        }
    }

    private function checkSubdirectory(InputInterface $input, SymfonyStyle $io) {
        $this->outputsubfolder = $input->getOption('outputsubfolder');
        if (empty($this->outputsubfolder)) {
            $this->outputsubfolder = hrtime(true);
        }

        if (is_dir($this->outputFolder . DIRECTORY_SEPARATOR . $this->outputsubfolder)) {
            if (!$io->confirm('Working directory already exists. Are you sure you want to overwrite?', false)) {
                $this->interact = Command::SUCCESS;
                return;
            }
        } else {
            mkdir($this->outputFolder . DIRECTORY_SEPARATOR . $this->outputsubfolder);
        }
    }

    private function checkWorkspace(InputInterface $input, SymfonyStyle $io) {
        $selected_workspace = $input->getOption('workspace');
        $this->workspace = $this->exporter->getWorkspaces();
        if (empty($selected_workspace)) {
            if (count($this->workspace) == 1) {
                $this->workspace = $this->workspace[0];
                $io->getErrorStyle()->info('Only 1 workspace found. Selecting \'' . $this->workspace->name . '\' by default.');
            } else {
                $options = [];
                foreach ($this->workspace as $row) {
                    $options[] = $row->name;
                }
                $selected_workspace = $io->choice('Please select the workspace you want to export or choose \'all\' (default is all)', array_merge(['all'], $options), 0);
                if ($selected_workspace == ['all']) {
                    $selected_workspace = 'all';
                }
            }
        }
        if (!empty($selected_workspace) && $selected_workspace != 'all') {
            $tmp = [];
            foreach ($this->workspace as $row) {
                if (is_array($selected_workspace)) {
                    if (in_array($row->name, $selected_workspace)) {
                        $tmp[] = $row;
                    }
                } elseif ($selected_workspace == $row->name) {
                    $tmp[] = $row;
                }
            }
            if ($tmp == [] && count($this->workspace)) {
                $io->getErrorStyle()->error('Unable to locate your workspace. Check the name and try again or omit passing the workspace to get a list to chose from.');
                $this->interact = Command::INVALID;
                return;
            } elseif ($tmp == []) {
                $io->getErrorStyle()->error('No workspaces found.');
                $this->interact = Command::FAILURE;
                return;
            }
            $this->workspace = $tmp;
        }
    }

    private function checkTeam(InputInterface $input, SymfonyStyle $io) {
        $selected_team = $input->getOption('team');
        $this->team = $this->getTeamsForWorkspace($this->workspace);
        if (empty($selected_team)) {
            $options = [];
            foreach ($this->team as $row) {
                $options[] = $row->name;
            }
            $selected_team = $io->choice('Please select the team(s) you want to export or choose \'all\' (default is all)', array_merge(['all'], $options), 0, true);
            if ($selected_team == ['all']) {
                $selected_team = 'all';
            }
        }
        if (!empty($selected_team) && $selected_team != 'all') {
            $tmp = [];
            foreach ($this->team as $row) {
                if (is_array($selected_team)) {
                    if (in_array($row->name, $selected_team)) {
                        $tmp[] = $row;
                    }
                } elseif ($selected_team == $row->name) {
                    $tmp[] = $row;
                }
            }
            if ($tmp == [] && count($this->team)) {
                $io->getErrorStyle()->error('Unable to locate your team. Check the name and try again or omit passing the team to get a list to chose from.');
                $this->interact = Command::INVALID;
                return;
            } elseif ($tmp == []) {
                $io->getErrorStyle()->error('No teams found.');
                $this->interact = Command::FAILURE;
                return;
            }
            $this->team = $tmp;
        }
        $io->block('Selected teams:');
        $opts = [];
        foreach ($this->team as $t) {
            $opts[] = $t->name;
        }
        $io->listing($opts);
    }

    private function getProjectStatus(\stdClass $project) {
        $statuses = $this->exporter->getProjectStatus($project);
        $project->status = $statuses;
        return $project;
    }

    private function checkProjects(InputInterface $input, SymfonyStyle $io) {
        $selected_project = $input->getOption('project');
        $this->project = $this->getProjectsForTeam($this->team);
        if (empty($selected_project)) {
            $options = [];
            foreach ($this->project as $key => $row) {
                $bad = 'https://gcc01.safelinks.protection.outlook.com/?url=http';
                if (strncmp($row->name, $bad, strlen($bad)) !== 0 && strlen($row->name) < 100) {
                    $options[] = $row->name;
                } else {
                    unset($this->project[$key]);
                }
            }
            $selected_project = $io->choice('Please select the project you want to export or choose \'all\' (default is all)', array_merge(['all'], $options), 0, true);
            if ($selected_project == ['all']) {
                $selected_project = 'all';
            }
        }
        if (!empty($selected_project && $selected_project != 'all')) {
            $tmp = [];
            foreach ($this->project as $row) {
                if (is_array($selected_project)) {
                    if (in_array($row->name, $selected_project)) {
                        if ($this->includeStatuses) {
                            $tmp[] = $this->getProjectStatus($row);
                        } else {
                            $tmp[] = $row;
                        }
                    }
                } elseif ($selected_project == $row->name) {
                    if ($this->includeStatuses) {
                        $tmp[] = $this->getProjectStatus($row);
                    } else {
                        $tmp[] = $row;
                    }
                }
                
            }
            if ($tmp == [] && count($this->project)) {
                $io->getErrorStyle()->error('Unable to locate your project. Check the name and try again or omit passing the workspace to get a list to choose from.');
                $this->interact = Command::FAILURE;
                return;
            }
            $this->project = $tmp;
        }
        $io->block('Selected projects:');
        $opts = [];
        foreach ($this->project as $p) {
            $opts[] = $p->name;
        }
        $io->listing($opts);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void {
        $this->currentSection->clear();
        $this->interact = Command::SUCCESS;
        $io = new SymfonyStyle($input, $output);
        $io->section('Pre-run checks');
        $curProgress = $io->createProgressBar();
        $curProgress->start(3);
        // check for available token
        $token = $input->getArgument('token');
        if (empty($token)) {
            $token = $this->config['token'];
        }
        if (empty($token)) {
            $token = $io->askHidden('Asana Authentication Token');
        }
        if (empty($token)) {
            $io->getErrorStyle()->error('You must set an authentication token to connect to Asana.');
            $this->interact = Command::INVALID;
            return;
        }
        $this->exporter->setToken($token);
        $curProgress->advance();
        $this->checkDirectory($input, $io);
        $curProgress->advance();
        $this->checkSubdirectory($input, $io);
        $curProgress->finish();
        $io->newLine();
        $curProgress = $io->createProgressBar();
        $curProgress->setFormat('verbose_with_message');
        $curProgress->start(3);
        $curProgress->setMessage('Workspace');
        $io->newLine(2);
        $io->section('Workspace');
        $this->checkWorkspace($input, $io);
        $curProgress->advance();
        $curProgress->setMessage('Team(s)');
        $io->newLine(2);
        $io->section('Team(s)');
        $this->checkTeam($input, $io);
        $curProgress->advance();
        $curProgress->setMessage('Project(s)');
        $io->newLine(2);
        $io->section('Project(s)');
        $this->includeStatuses = $input->getOption('include_projectstatus');
        if (empty($this->includeStatuses)) {
            $this->includeStatuses = $this->config['output']['include_statusupdates'];
        }
        $this->checkProjects($input, $io);
        $curProgress->advance();
        $curProgress->setMessage('complete');
        $this->includeSubtasks = $input->getOption('include_subtasks');
        if (empty($this->includeSubtasks)) {
            $this->includeSubtasks = $this->config['output']['include_subtasks'];
        }
        $this->includeAttachments = $input->getOption('include_attachments');
        if (empty($this->includeAttachments)) {
            $this->includeAttachments = $this->config['output']['include_attachments'];
        }
        $curProgress->finish();
    }

    private function export(OutputInterface $output, SymfonyStyle $io): void {
        $this->currentSection->clear();
        $projectsection = $output->section();
        $projectsection->writeln('Projects:');
        $projectprogress = new ProgressBar($projectsection);
        $projectprogress->setOverwrite(true);
        $projectprogress->setMaxSteps(count($this->project));
        $projectprogress->setFormat('verbose_with_message');
        $projectprogress->setMessage('');
        $projectprogress->start();
        $tasksection = $output->section('Tasks');
        $tasksection->writeln('Tasks:');
        $taskprogress = new ProgressBar($tasksection);
        $taskprogress->setOverwrite(true);
        $taskprogress->setFormat('verbose_with_message_nomax');
        $taskprogress->setMessage('');
        $taskprogress->start();
        $attachmentsection = $output->section('Attachments');
        $attachmentsection->writeln('Attachments:');
        $attachmentprogress = new ProgressBar($attachmentsection);
        $attachmentprogress->setOverwrite(true);
        $attachmentprogress->setFormat('verbose_with_message_nomax');
        $attachmentprogress->setMessage('');
        $attachmentprogress->start();
        $basedir = $this->outputFolder . DIRECTORY_SEPARATOR . $this->outputsubfolder;
        //$io->newLine();
        foreach ($this->project as $p) {
            $projectprogress->setMessage($p->name);
            if (!isset($p->team)) {
                $io->error('Team wasn\'t set for project: ' . $p->name);
                $projectprogress->advance();
                continue;
            }
            $projectdir = $basedir . DIRECTORY_SEPARATOR . urlencode($p->team->name) . DIRECTORY_SEPARATOR . urlencode($p->name);
            if (!is_dir($projectdir)) {
                mkdir($projectdir, 0777, true);
            }
            $res = $this->exporter->exportProjectTasks($projectdir,
                    ['include_subtasks' => $this->includeSubtasks, 'projects' => [$p], 'progress' => $taskprogress]);
            sleep(1);
            if ($this->includeAttachments) {
                if (!is_dir($projectdir . DIRECTORY_SEPARATOR . 'attachments')) {
                    mkdir($projectdir . DIRECTORY_SEPARATOR . 'attachments', 0777, true);
                }
                $this->exporter->fetchAttachments($projectdir, [], $attachmentprogress, ['progress' => $attachmentprogress]);
                sleep(1);
            }
            $this->exporter->writeProject($projectdir, $p, isset($p->status) ? $p->status : []);
            $this->exporter->clearAttachmentCache();
            $projectprogress->advance();
            //sleep(max(round(rand(0, 85) / 100),0));
        }
        $projectprogress->finish();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {

        $io = new SymfonyStyle($input, $output);
        if ($this->initialize != Command::SUCCESS && !is_null($this->initialize)) {
            return $this->initialize;
        }
        if (!$this->interact == Command::SUCCESS && !is_null($this->interact)) {
            return $this->interact;
        }
        if (is_null($this->outputFolder) || is_null($this->outputsubfolder) ||
                is_null($this->includeAttachments) || is_null($this->workspace) ||
                is_null($this->team) || is_null($this->project) ||
                is_null($this->compressEnabled) || is_null($this->includeSubtasks)) {
            $io->error('Configuration was not complete. Check your config and trying again.');
            return Command::FAILURE;
        }
        $io->newLine(2);
        $io->title('Export');
        $io->info('This may take awhile depending upon the number of items to export');
        $this->export($output, $io);
        $io->newLine();
        $this->compressOutput($this->outputFolder . DIRECTORY_SEPARATOR . $this->outputsubfolder, $io);
        $io->newLine();
        return Command::SUCCESS;
    }
    
    private function compressOutput($folder, SymfonyStyle $io) {
        $io->newLine(2);
        $io->title('Compression');
        if (!$this->compressEnabled) {
            $io->getErrorStyle()->info('Compression disabled');
            $io->note('Output saved to ' . $folder);
        } else {
            $this->exporter->compressOutput($folder, $this->outputFolder . DIRECTORY_SEPARATOR . 'export-' . $this->outputsubfolder . '.zip');
            $io->note('Output saved to ' . realpath($this->outputFolder) . DIRECTORY_SEPARATOR . 'export-' . $this->outputsubfolder . '.zip');
            if ($this->cleanAfterCompress) {
                $files = new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($folder, \RecursiveDirectoryIterator::SKIP_DOTS),
                        \RecursiveIteratorIterator::CHILD_FIRST
                );

                foreach ($files as $fileinfo) {
                    $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                    $todo($fileinfo->getRealPath());
                }
                rmdir($folder);
            }
        }
    }

    private function getProjectsForTeam(mixed $team) {
        if (is_array($team)) {
            $ret = [];
            foreach ($team as $row) {
                $res = $this->getProjectsForTeam($row);
                foreach ($res as $project) {
                    $project->team = $row;
                    $ret[] = $project;
                }
            }
            return $ret;
        }

        return $this->exporter->getTeamProjects($team);
    }

    private function getTeamsForWorkspace(mixed $workspace) {
        if (is_array($workspace)) {
            $ret = [];
            foreach ($workspace as $row) {
                $res = $this->getTeamsForWorkspace($row);
                foreach ($res as $team) {
                    $res[] = $team;
                }
            }
            return $ret;
        }

        return $this->exporter->getTeams($workspace);
    }
}
