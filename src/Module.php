<?php

/**
 * Description of Module
 * 
 * PHP version 8
 * 
 * * * License * * * 
 * Copyright (C) 2024 Andrew Wallace <andrew.wallace@portospire.com>.
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
 * @author    Andrew Wallace <andrew.j.wallace4@gmail.com>
 * @copyright 2024 Andrew Wallace <andrew.j.wallace@gmail.com>
 * @license   LGPL2.1
 * @version   GIT: $ID$
 * @link      https://github.com/criterion9/asanadataexporter
 */

namespace Criterion9\AsanaDataExporter;

/**
 * Description of Module
 *
 * @category  CategoryName
 * @package   PackageName
 * @author    Andrew Wallace <andrew.j.wallace4@gmail.com>
 * @copyright 2024 Andrew Wallace <andrew.j.wallace4@gmail.com>
 * @license   LGPL2.1
 * @version   Release: @package_version@
 * @link      https://github.com/criterion9/asanadataexporter/src/Module.php
 * @since     Class available since Release 0.0.0
 */
class Module {

    const VERSION = '@release_version@';

    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }
}
