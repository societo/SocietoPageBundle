<?php

/**
 * SocietoPageBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Societo\BaseBundle\Util\ArrayAccessibleParameterBag;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FrontendController extends Controller
{
    public function handleAction($name, $parameters = null)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $token = $this->get('security.context')->getToken();

        $page = $em->getRepository('SocietoPageBundle:Page')->findOneBy(array(
            'name' => $name,
        ));
        if (!$page) {
            throw $this->createNotFoundException();
        }

        if (!$page->getPublished()) {
            if ($token && false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
                throw new AccessDeniedException();
            }
        }

        if ($token && false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            if (!$page->isAnonymousAccess()) {
                $this->get('session')->setFlash('notice', 'You must login to continue this action');

                throw new AccessDeniedException();
            }
        }

        $gadgets = $em->getRepository('SocietoPageBundle:PageGadget')->getByPage($page);
        $gadgets = array_merge(array('main_content' => array(), 'sub_content' => array(), 'head' => array(), 'foot' => array(), 'side' => array()), $gadgets);

        if (!$parameters) {
            $parameters = new ArrayAccessibleParameterBag();
        }

        $titleParams = array();
        foreach ($this->get('request')->attributes->all() as $k => $v)
        {
            $titleParams['{'.$k.'}'] = $v;
        }

        $title = strtr($page->getTitle(), $titleParams);

        return $this->render('SocietoPageBundle:Frontend:handle.html.twig', array(
            'title'             => $title,
            'page'              => $page,
            'gadgets'           => $gadgets,
            'parent_attributes' => $this->get('request')->attributes,
            'parameters'        => $parameters,
        ));
    }
}
