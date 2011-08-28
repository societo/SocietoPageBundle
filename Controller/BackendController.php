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

class BackendController extends Controller
{
    public function listAction()
    {
        $pages = $this->get('doctrine.orm.entity_manager')->getRepository('SocietoPageBundle:Page')->findAll();

        return $this->render('SocietoPageBundle:Backend:list.html.twig', array(
            'pages' => $pages,
        ));
    }

    public function deleteAction($name)
    {
        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');

        $page = $em->getRepository('SocietoPageBundle:Page')->findOneBy(array(
            'name' => $name,
        ));

        if (!$page) {
            throw $this->createNotFoundException();
        }

        $form = $this->get('form.factory')->create('form');

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $config = $this->getPageConfig();
                $this->removeEntryFromPageConfig($config, $page->getName());

                $em->remove($page);
                $em->persist($config);
                $em->flush();

                $this->get('session')->setFlash('success', 'Removed data successfully.');

                return $this->redirect($this->generateUrl('_backend_page'));
            }
        }

        return $this->render('SocietoPageBundle:Backend:delete.html.twig', array(
            'form' => $form->createView(),
            'page' => $page,
        ));
    }

    public function editAction($name = null)
    {
        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');

        if ($name) {
            $page = $em->getRepository('SocietoPageBundle:Page')->findOneBy(array(
                'name' => $name,
            ));

            if (!$page) {
                throw $this->createNotFoundException();
            }
        } else {
            $page = new \Societo\PageBundle\Entity\Page($name);
        }

        $form = $this->get('form.factory')
            ->create(new \Societo\PageBundle\Form\PageType(), $page);

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $page = $form->getData();
                $config = $this->getPageConfig();
                // TODO
                $this->addEntryToPageConfig($config, $page->getName(), array(
                    'pattern' => $page->getPath(),
                    'defaults' => array(
                        '_controller' => 'SocietoPageBundle:Frontend:handle',
                        'name' => $page->getName(),
                    ),
                    'requirements' => array(),
                    'options' => array(),
                ));

                $em->persist($page);
                $em->persist($config);
                $em->flush();

                $this->get('session')->setFlash('success', 'Settings are saved successfully.');

                return $this->redirect($this->generateUrl('_backend_page'));
            }
        }

        return $this->render('SocietoPageBundle:Backend:create.html.twig', array(
            'form' => $form->createView(),
            'page' => $page,
            'available_parameters' => $this->get('societo.page.parameter.manager')->getParameters(),
        ));
    }

    public function plotAction($name)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $page = $em->getRepository('SocietoPageBundle:Page')->findOneBy(array(
            'name' => $name,
        ));

        $gadgets = $em->getRepository('SocietoPageBundle:PageGadget')->getByPage($page);
        $gadgets = array_merge(array('main_content' => array(), 'sub_content' => array(), 'head' => array(), 'foot' => array(), 'side' => array()), $gadgets);

        $changelist = array();
        foreach ($gadgets as $k => $v) {
            $_t = array();
            foreach ($v as $_g) {
                $_t[] = 'gadget_'.$_g->getId();
            }
            $changelist[$k] = implode(',', $_t);
        }

        return $this->render('SocietoPageBundle:Backend:edit.html.twig', array(
            'page'       => $page,
            'gadgets'    => $gadgets,
            'changelist' => $changelist,
            'available_gadgets' => $this->get('societo.page.gadget.manager')->getGadgets(),
        ));
    }

    public function updateAction($name)
    {
        $request = $this->get('request');
        if ('POST' !== $request->getMethod()) {
            throw $this->createNotFoundException();
        }

        $em = $this->get('doctrine.orm.entity_manager');

        $page = $em->getRepository('SocietoPageBundle:Page')->findOneBy(array(
            'name' => $name,
        ));
        $page->setPath($_POST['path']);
        $config = $this->getPageConfig();
        $this->addEntryToPageConfig($config, $name, array(
            'pattern' => $_POST['path'],
            'defaults' => array(
                '_controller' => 'SocietoPageBundle:Frontend:handle',
                'name' => $name,
            ),
            'requirements' => array(),
            'options' => array(),
        ));

        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($page);
        $em->persist($config);
        $em->flush();

        $this->get('session')->setFlash('success', 'Page was updated successfully.');
        return $this->redirect($this->generateUrl('_backend_page'));
    }

    public function addGadgetAction($page_name, $gadget_name)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $request = $this->get('request');

        $point = 'head';
        $gadgetName = $gadget_name;

        $page = $em->getRepository('SocietoPageBundle:Page')->findOneBy(array(
            'name' => $page_name,
        ));
        $gadget = new \Societo\PageBundle\Entity\PageGadget($page, $point, $gadgetName);

        $gadgetInfo = $this->get('societo.page.gadget.manager')->getGadget($gadget->getName());
        if ($gadgetInfo) {
            $gadgetOptions = $gadgetInfo->getOptions();
        } else {
            $gadgetInfo = $this->get('kernel')->getBundle('SocietoPageBundle')
                ->getPageGadget($gadget->getName());
            $gadgetOptions = $gadgetInfo['parameters'];
        }

        $form = $this->get('form.factory')
            ->create(new \Societo\PageBundle\Form\PageGadgetType($gadgetOptions))
            ->setData($gadget);

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $em->persist($form->getData());
                $em->flush();

                $this->get('session')->setFlash('success', 'Changes are saved successfully');

                return $this->redirect($this->generateUrl('_backend_page_edit', array(
                    'name' => $form->getData()->getPage()->getName(),
                )));
            }
        }

        return $this->render('SocietoPageBundle:Backend:add_gadget.html.twig', array(
            'page_name'   => $page_name,
            'gadget_name' => $gadget_name,
            'form'        => $form->createView(),
            'gadget_info' => $gadgetInfo,
        ));
    }

    public function editGadgetAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $request = $this->get('request');

        $gadget = $em->getRepository('SocietoPageBundle:PageGadget')->find($id);
        if (!$gadget) {
            throw $this->createNotFoundException();
        }

        $gadgetInfo = $this->get('societo.page.gadget.manager')->getGadget($gadget->getName());
        if ($gadgetInfo) {
            $gadgetOptions = $gadgetInfo->getOptions();
        } else {
            $gadgetInfo = $this->get('kernel')->getBundle('SocietoPageBundle')
                ->getPageGadget($gadget->getName());
            $gadgetOptions = $gadgetInfo['parameters'];
        }

        $form = $this->get('form.factory')
            ->create(new \Societo\PageBundle\Form\PageGadgetType($gadgetOptions))
            ->setData($gadget);

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $em->persist($form->getData());
                $em->flush();

                $this->get('session')->setFlash('success', 'Changes are saved successfully');

                return $this->redirect($this->generateUrl('_backend_page_edit', array(
                    'name' => $form->getData()->getPage()->getName(),
                )));
            }
        }

        return $this->render('SocietoPageBundle:Backend:edit_gadget.html.twig', array(
            'gadget' => $gadget,
            'form'   => $form->createView(),
            'gadget_info' => $gadgetInfo,
        ));
    }

    public function deleteGadgetAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $request = $this->get('request');

        $gadget = $em->getRepository('SocietoPageBundle:PageGadget')->find($id);
        if (!$gadget) {
            throw $this->createNotFoundException();
        }

        $gadgetInfo = $this->get('societo.page.gadget.manager')->getGadget($gadget->getName());
        if ($gadgetInfo) {
            $gadgetOptions = $gadgetInfo->getOptions();
        } else {
            $gadgetInfo = $this->get('kernel')->getBundle('SocietoPageBundle')
                ->getPageGadget($gadget->getName());
            $gadgetOptions = $gadgetInfo['parameters'];
        }

        $form = $this->get('form.factory')->create('form');

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em->remove($gadget);
                $em->flush();

                $this->get('session')->setFlash('success', 'Removed data successfully.');

                return $this->redirect($this->generateUrl('_backend_page'));
            }
        }

        return $this->render('SocietoPageBundle:Backend:delete_gadget.html.twig', array(
            'form' => $form->createView(),
            'gadget' => $gadget,
            'gadget_info' => $gadgetInfo,
        ));
    }

    public function sortGadgetAction($page_name)
    {
        $request = $this->get('request');
        if ('POST' !== $request->getMethod()) {
            throw $this->createNotFoundException();
        }

        $em = $this->get('doctrine.orm.entity_manager');

        $page = $em->getRepository('SocietoPageBundle:Page')->findOneBy(array(
            'name' => $page_name,
        ));

        $positions = array(
            'head'  => array_flip((array)explode(',', $_POST['gadget_position_head'])),
            'main_content'  => array_flip((array)explode(',', $_POST['gadget_position_main_content'])),
            'sub_content' => array_flip((array)explode(',', $_POST['gadget_position_sub_content'])),
            'side' => array_flip((array)explode(',', $_POST['gadget_position_side'])),
            'foot' => array_flip((array)explode(',', $_POST['gadget_position_foot'])),
        );

        foreach ($page->getGadgets() as $gadget) {
            foreach ($positions as $k => $v) {
                if (isset($v['gadget_'.$gadget->getId()])) {
                    $gadget->setPoint($k);
                    $gadget->setSortOrder($v['gadget_'.$gadget->getId()]);
                }
            }

            $em->persist($gadget);
        }

        $em->flush();

        $this->get('session')->setFlash('success', 'Changes are saved successfully');

        return $this->redirect($this->generateUrl('_backend_page_edit', array(
            'name' => $page_name,
        )));
    }

    private function getPageConfig()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $config = $em->getRepository('SocietoConfigDatabaseBundle:Config')->findOneBy(array(
            'name' => 'routing',
        ));
        if (!$config) {
            $config = new \Societo\Config\DatabaseBundle\Entity\Config('routing');
        }

        return $config;
    }

    private function removeEntryFromPageConfig($config, $key)
    {
        $list = $config->getValue();
        if (!$list) {
            $list = array();
        }

        unset($list[$key]);

        $config->setValue($list);
    }

    private function addEntryToPageConfig($config, $key, $entry)
    {
        $list = $config->getValue();
        if (!$list) {
            $list = array();
        }

        if (preg_match_all('/\{([^\}]+\?)\}/', $entry['pattern'], $matches)) {
            foreach ($matches[1] as $match) {
                $param = substr($match, 0, -1);
                $entry['pattern'] = str_replace($match, $param, $entry['pattern']);
                $entry['defaults'][$param] = null;
            }
        }

        $list[$key] = $entry;

        $config->setValue($list);
    }
}
