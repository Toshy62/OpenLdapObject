<?php
/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 Pierre Pélisset
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

namespace OpenLdapObject;

use OpenLdapObject\Collection\ArrayCollection;
use OpenLdapObject\Manager\EntityCollection;
use OpenLdapObject\Manager\Hydrate\Hydrater;

/**
 * Class Entity
 * @package OpenLdapObject
 */
abstract class Entity
{
    private $_dn;
    private $_originData;

    /**
     * @var ArrayCollection
     */
    private $objectClass;

    public function __construct()
    {
        $this->objectClass = new ArrayCollection();

        $hydrater = new Hydrater(get_class($this));
        $hydrater->defineCollection($this);
        $hydrater->defineObjectClass($this);
    }

    public final function _setDn($dn)
    {
        $this->_dn = $dn;
    }

    public final function _getDn()
    {
        return $this->_dn;
    }

    public final function _setOriginData(array $originData)
    {
        $this->_originData = $originData;
    }

    public final function _getOriginData()
    {
        return $this->_originData;
    }

    public function getObjectClass()
    {
        return $this->objectClass;
    }

    public function addObjectClass($objectClass)
    {
        $this->objectClass->add($objectClass);
        return $this;
    }

    public function removeObjectClass($objectClass)
    {
        $this->objectClass->remove($objectClass);
        return $this;
    }
} 