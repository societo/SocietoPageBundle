<?php

/**
 * SocietoPageBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\PageBundle\PageGadget;

/**
 *
 * @author Kousuke Ebihara <ebihara@php.net>
 */
class Manager
{
    private $gadgets;

    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function addGadget($gadget)
    {
        $gadget->setContainer($this->container);

        $this->gadgets[$gadget->getName()] = $gadget;
    }

    public function getGadget($name)
    {
        if (!isset($this->gadgets[$name])) {
            return null;
        }

        return $this->gadgets[$name];
    }

    public function getGadgets()
    {
        return $this->gadgets;
    }
}
