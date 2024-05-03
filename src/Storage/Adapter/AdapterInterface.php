<?php

/**
 * Description of AdapterInterface
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

/**
 * Description of AdapterInterface
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
interface AdapterInterface {

    /**
     * Retrieves an item from the storage object
     * 
     * @param string $key Key for content
     * @param string $ext Extension for content
     * 
     * @return string | boolean
     */
    public function get(string $key, string $ext = '');

    /**
     * Stores an item to the storage object
     * 
     * @param string $key             The key for the content
     * @param mixed  $content         The content to store
     * @param string $ext             The extension of the content
     * @param bool   $overwrite       Whether to overwrite if the file already exists
     * @param string $access          What access the file should have (public|private)
     * 
     * @return boolean
     */
    public function put(string $key, $content, string $ext = '', bool $overwrite = false, string $access = 'public'): bool;

    /**
     * Returns the content size of a stored object
     * 
     * @param string $key The key for the content
     * @param string $ext The extension of the content
     * 
     * @return int
     */
    public function contentLength(string $key, string $ext = ''): int;
}
