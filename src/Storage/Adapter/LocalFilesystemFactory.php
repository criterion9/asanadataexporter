<?php

/**
 * Description of LocalFilesystemFactory
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

namespace Criterion9\AsanaDataExporter\Storage\Adapter;

use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Laminas\Stdlib\ArrayUtils;


/**
 * Description of LocalFilesystemFactory
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
class LocalFilesystemFactory implements AbstractFactoryInterface {

    /**
     * Constructor for LocalFilesystemFactory
     * 
     * @param \Interop\Container\ContainerInterface $container     Container object
     * @param type                                  $requestedName Object creating
     * @param array                                 $options       Array of options
     * 
     * @return LocalFilesystem
     */
    public function __invoke(\Interop\Container\ContainerInterface $container,
            $requestedName, array $options = null
    ): mixed {

        $config = $container->get('config');
        return new LocalFilesystem($config);
    }

    /**
     * Provides v2 way to init. not making compatible.
     * 
     * @param \Interop\Container\ContainerInterface $container     Container object
     * @param type                                  $requestedName Requested object
     * 
     * @return void
     */
    public function canCreate(\Interop\Container\ContainerInterface $container,
            $requestedName
    ): bool {
        
    }
}
