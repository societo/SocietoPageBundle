<?php

/**
 * SocietoPageBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\PageBundle\Twig\Extension;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Societo\BaseBundle\Util\ArrayAccessibleParameterBag;

class PageExtension extends \Twig_Extension
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getName()
    {
        return 'societo_page';
    }

    public function getFunctions()
    {
        return array(
            'render_gadget' => new \Twig_Function_Method($this, 'renderGadget'),
        );
    }

    public function renderGadget($gadget, $parentAttributes, ArrayAccessibleParameterBag $parameters)
    {
        $manager = $this->container->get('societo.page.gadget.manager');

        $device = $gadget->getParameter('device', array('full', 'mobile'));
        $flavour = $this->container->get('request')->attributes->get('_flavour');
        if ($device && !in_array($flavour, $device)) {
            return null;
        }

        $options = array(
            'attributes' => array(
                'gadget'           => $gadget,
                'parentAttributes' => $parentAttributes,
                'parameters'       => $parameters,
            ),

            'query' => null,  // avoid reset query by request duplicating (is this a symfony's bug?)
        );

        $controller = $gadget->getName();
        if ($manager->getGadget($controller)) {
            $controller = $manager->getGadget($controller);
        }

        return $this->container->get('http_kernel')->render($controller, $options);
    }
}
