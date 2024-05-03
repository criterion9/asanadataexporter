<?php

/**
 * Description of SQLite
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
 * @license   LGPL21
 * @version   GIT: $ID$
 * @link      
 */

namespace Criterion9\AsanaDataExporter\Storage;

use Exception;

/**
 * Description of SQLite
 *
 * @category  CategoryName
 * @package   PackageName
 * @author    Andrew Wallace <criterion9@proton.me>
 * @copyright 2024 Andrew Wallace <criterion9@proton.me>
 * @license   LGPL21
 * @version   Release: @package_version@
 * @link      
 * @since     Class available since Release 0.0.0
 */
class SQLite extends \SQLite3 {

    protected $initialized, $file;

    function __construct(string $workingdirectory) {
        if (!is_dir($workingdirectory)) {
            throw new Exception('Working directory does not exist. Cannot initiate local session.');
        }
        $this->file = realpath($workingdirectory . DIRECTORY_SEPARATOR . '../') . DIRECTORY_SEPARATOR . 'session.db';
        $this->initialized = file_exists($this->file);
        $this->open($this->file);
        if (!$this->initialized) {
            $this->initialize();
        }
    }

    public function clearDB() {
        if (isset($this->file) && is_file($this->file)) {
            unlink($this->file);
        }
    }

    protected function initialize() {
        //$this->exec('CREATE TABLE projects (gid TEXT PRIMARY KEY NOT NULL,'
        //      . 'OBJ BLOG NOT NULL);');
        $this->exec('CREATE TABLE tasks (gid TEXT PRIMARY KEY NOT NULL,'
                . 'obj BLOB NOT NULL);');
    }

    public function saveTask(array $task) {
        $stmt = $this->prepare('INSERT INTO tasks (gid, obj) VALUES (?,?)');
        $stmt->bindValue(1, $task['gid']);
        $stmt->bindValue(2, json_encode($task));
        $stmt->execute();
        //$this->query('INSERT INTO tasks (gid, obj) VALUES (\'' . $this->escapeString($task['gid']) . '\', "' . json_encode($task) . '\')');
    }

    public function getTask(string $gid): array|\stdClass|bool {
        if (!empty($gid)) {
            $res = $this->querySingle('SELECT gid, obj FROM tasks WHERE gid=\'' . $this->escapeString($gid) . '\'', true);
            if ($res) {
                return json_decode($res['obj']);
            }
        }
        return false;
    }
}
