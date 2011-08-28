<?php

/**
 * SocietoPageBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\PageBundle\Routing;

use Societo\PageBundle\Event\FilterRoutingParameterEvent;

class SelectableParameterManager
{
    private $dispatcher;

    private $parameters = array();

    public function __construct($dispatcher)
    {
        $this->dispatcher = $dispatcher;

        $event = new FilterRoutingParameterEvent($this);
        $this->dispatcher->dispatch('onSocietoRoutingParameterBuild', $event);
    }

    public function setParameter($parameter, $description = '')
    {
        $this->parameters[$parameter] = $description;
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}
