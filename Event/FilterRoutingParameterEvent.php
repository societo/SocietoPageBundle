<?php

/**
 * SocietoPageBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\PageBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class FilterRoutingParameterEvent extends Event
{
    private $manager;

    public function __construct($manager)
    {
        $this->manager = $manager;
    }

    public function getManager()
    {
        return $this->manager;
    }
}
