<?php

use Criterion9\AsanaDataExporter\Storage\Adapter\LocalFilesystem;

/**
 * Description of module
 * 
 * PHP version 8
 * 
 * * * LICENSE * * * 
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
 * * * END LICENSE * * * 
 * 
 * @category  CategoryName
 * @package   PackageName
 * @author    Andrew Wallace <criterion9@proton.me>
 * @copyright 2024 Andrew Wallace <criterion9@proton.me>
 * @license   LGPL2.1
 * @version   GIT: $ID$
 * @link      
 */
$ret = [
    'asanadataexporter' => [
        'output' => [
            'createifnotexist' => true,
            'include_subtasks' => true,
            'include_attachments' => true,
            'include_statusupdates' => true,
            'include_json' => true,
            'include_csv' => true,
            'defaultlocation' => realpath(__DIR__).'/../asanaexport',
            'compress' => true,
            'cleanaftercompress' => true,
            'adapter' => LocalFilesystem::class
        ],
        'useLocalSession' => true,
        'token' => null
    ]
];

if(file_exists(realpath(__DIR__) . '/.asana_token')){
    $ret['asanadataexporter']['token'] = file(realpath(__DIR__) . '/.asana_token')[0];
}
return $ret;
