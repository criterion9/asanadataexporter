<?php

/**
 * Description of AbstractBase
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

use Laminas\Hydrator\HydratorInterface;
use Laminas\Stdlib\ArraySerializableInterface;

/**
 * Description of AbstractBase
 *
 * @category  CategoryName
 * @package   PackageName
 * @author    Andrew Wallace <andrew.wallace@portospire.com>
 * @copyright 2024 Andrew Wallace <andrew.wallace@portospire.com>
 * @license   LGPL21
 * @version   Release: @package_version@
 * @link      
 * @since     Class available since Release 0.0.0
 */
abstract class AbstractBase implements ArraySerializableInterface {
    public function __get($name)
    {
        if ($name == 'inputFilter' || $name == 'input_filter') {
            return false;
        }
        if (property_exists(get_class($this), $name)) {
            return $this->$name;
        }
        return false;
    }

    public function __isset($name)
    {
        if ($name == 'inputFilter' || $name == 'input_filter') {
            return false;
        }
        if (property_exists(get_class($this), $name)) {
            return true;
        }
        return false;
    }

    public function __set($name, $value)
    {
        if ($name == 'inputFilter' || $name == 'input_filter') {
            return false;
        }
        if (property_exists(get_class($this), $name)) {
            $this->$name = $value;
        }
    }

    public function exchangeArray(array $data)
    {
        if ($this instanceof HydratorInterface) {
            return $this->hydrate($data, $this);
        }
        $arr = get_object_vars($this);
        unset($arr['inputFilter']);
        unset($arr['input_filter']);
        foreach ($arr as $key => $element) {
            if (isset($data[$key])) {
                $this->$key = $data[$key];
            } else {
                $this->$key = $element;
            }
        }
    }

    public function getArrayCopy()
    {
        $arr = get_object_vars($this);
        unset($arr['inputFilter']);
        unset($arr['input_filter']);
        return $arr;
    }
    
}
