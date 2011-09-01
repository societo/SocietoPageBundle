<?php

/**
 * SocietoPageBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\PageBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Societo\PageBundle\DependencyInjection\Compiler\GadgetPass;

/**
 * SocietoPageBundle
 *
 * @author Kousuke Ebihara <ebihara@php.net>
 */
class SocietoPageBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new GadgetPass());
    }

    public function boot()
    {
        $dispatcher = $this->container->get('event_dispatcher');

        $dispatcher->addListener('onSocietoMatchedRouteParameterFilter', function ($event) {
            $parameters = $event->getParameters();
            $em = $event->getEntityManager();

            if (isset($parameters['gadget'])) {
                $parameters['gadget'] = $em->getRepository('SocietoPageBundle:PageGadget')->find($parameters['gadget']);
                if (!$parameters['gadget']) {
                    throw new \Exception('Gadget not found');
                }
            }

            $event->setParameters($parameters);
        });

        $dispatcher->addListener('onSocietoGeneratingRouteParameterFilter', function ($event) {
            $parameters = $event->getParameters();
            $em = $event->getEntityManager();

            if (isset($parameters['gadget'])) {
                $parameters['gadget'] = $parameters['gadget']->getId();
            }

            $event->setParameters($parameters);
        });
    }
}
