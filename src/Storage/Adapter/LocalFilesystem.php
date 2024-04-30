<?php

/**
 * Description of LocalFilesystem
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
 * Description of LocalFilesystem
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
class LocalFilesystem implements AdapterInterface{
    protected $save_prefix = '',
    $final_location = '.';

    /**
     * Constructor for LocalFilesystem
     * 
     * @param array $config Contains a config array with settings options
     */
    public function __construct(array $config)
    {
        if (isset($config['save_prefix'])) {
            $this->save_prefix = $config['save_prefix'];
        }
        if (isset($config['final_location'])) {
            $this->final_location = $config['final_location'];
        }
    }

    /**
     * Fetches a stored object based on key/extension
     * 
     * @param string $key  the key of the object
     * @param string $ext1 optional extension of the object
     * 
     * @return string | boolean
     */
    public function get(string $key, string $ext1 = '')
    {
        $ext = !empty($ext1) ? '.' . $ext1 : '';
        $filename = $this->final_location . $this->save_prefix . $key . $ext;
        if (file_exists($filename)) {
            return file_get_contents($filename);
        }
        /*if (!empty($ext)) {
            return $this->get($key); //try again without the extension
        }*/
        return false;
    }

    /**
     * Stores an object by a key/extension
     * 
     * @param string $key             The key of the object to store
     * @param type   $source_filename The temporary filename to be stored
     * @param string $ext1            The extension of the object to store
     * @param bool   $overwrite       Whether to overwrite if the file already exists
     * @param string $access          What access the file should have (public|private)
     * 
     * @return boolean
     */
    public function put(string $key, $source_filename, string $ext1 = '', bool $overwrite = false, string $access = 'public'): bool
    {
        $ext = !empty($ext1) ? '.' . $ext1 : '';
        $dest_filename = $this->final_location . $this->save_prefix . $key . $ext;
        if (!file_exists($dest_filename) || ($overwrite && is_writable($dest_filename))) {
            $handle = fopen($dest_filename, 'cb');
            if (!$handle) {
                return false;
            }
            $lock = flock($handle, LOCK_EX);
            if ($lock) {
                ftruncate($handle, 0); //clear the file in case fopen timing couldn't
                $fp = fopen($source_filename, "rb");
                $content = fread($fp, filesize($source_filename));
                fwrite($handle, $content);
                unset($content);
                fflush($handle);
                fclose($fp);
            }
            flock($handle, LOCK_UN);
            fclose($handle);
            return true;

        }
        return false;
    }

    /**
     * Returns the content size of a stored object
     * 
     * @param string $key  The key of the stored object
     * @param string $ext1 The extension of the stored object
     * 
     * @return int
     */
    public function contentLength(string $key, string $ext1 = ''): int
    {
        $ext = !empty($ext1)? '.'.$ext1 : '';
        $filename = $this->final_location . $this->save_prefix . $key . $ext;
        if(file_exists($filename)){
            return filesize($filename);
        }
        if($ext !== ''){
            return $this->contentLength($key);
        }
        return 0;
    }
}
