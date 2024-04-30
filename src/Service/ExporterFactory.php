<?php

/**
 * Description of ExporterFactory
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

use Criterion9\AsanaDataExporter\Module;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Psr\Container\ContainerInterface;

/**
 * Description of ExporterFactory
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
class ExporterFactory implements AbstractFactoryInterface {

    //put your code here
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): mixed {
        $config = $container->get('config')['asanadataexporter'] ?? [];
        return new Exporter($config);
    }

    public function canCreate(ContainerInterface $container, $requestedName): bool {
        return true;
    }
}
