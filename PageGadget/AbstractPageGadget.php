<?php

/**
 * SocietoPageBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\PageBundle\PageGadget;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Societo\BaseBundle\Util\ArrayAccessibleParameterBag;

use Societo\PageBundle\Entity\PageGadget;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Abstract class of page gadgets.
 *
 * @author Kousuke Ebihara <ebihara@php.net>
 */
abstract class AbstractPageGadget extends Controller
{
    protected $caption;

    protected $description;

    /**
     * Main routine of this gadget.
     *
     * This method is called as controller from __invoke().
     *
     * @param PageGadget $gadget
     * @param ParameterBag $parentAttributes
     * @param ArrayAccessibleParameterBag $parameters
     * @return Symfony\Component\HttpFoundation\Response
     */
    abstract function execute($gadget, $parentAttributes, $parameters);

    /**
     * Gets caption of this gadget.
     *
     * All of gadgets must return caption by this method.
     * But usually you don't need to extend this method. You can define caption
     * by setting a value of $caption property.
     *
     * @return string
     */
    public function getCaption()
    {
        if (!$this->caption) {
            throw new \LogicException('caption must be defined');
        }

        return $this->caption;
    }

    /**
     * Gets description of this gadget.
     *
     * All of gadgets must return description by this method.
     * But usually you don't need to extend this method. You can define description
     * by setting a value of $description property.
     *
     * @return string
     */
    public function getDescription()
    {
        if (!$this->description) {
            throw new \LogicException('description must be defined');
        }

        return $this->description;
    }

    /**
     * Gets options of this gadget.
     *
     * (TODO: write description of this method)
     *
     * @return array
     */
    public function getOptions()
    {
        return array();
    }

    /**
     * Gets name of this gadget.
     *
     * @return string
     */
    public function getName()
    {
        $str = get_class($this);;
        $pieces = explode('\\', $str);

        if ('SocietoPlugin' === $pieces[0]) {
            array_shift($pieces);
        }

        $name = '';

        foreach ($pieces as $piece) {
            $name .= $piece;
            if (preg_match('#(Bundle|Plugin)$#', $piece)) {
                break;
            }
        }

        $name .= ':'.array_pop($pieces);

        return $name;
    }

    /**
     * Invoke execute() method as controller.
     *
     * And this method handles errors in processing execute().
     * Errors will render to error_gadget.html.twig template.
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function __invoke($gadget, $parentAttributes, ArrayAccessibleParameterBag $parameters)
    {
        try {
            return $this->execute($gadget, $parentAttributes, $parameters);
        } catch (\Exception $e) {
            return $this->render('SocietoPageBundle:Gadget:error_gadget.html.twig', array(
                'gadget'    => $gadget,
                'exception' => $e,
                'exception_class_name' => get_class($e),
            ));
        }
    }

    /**
     * Get text representaion of this class.
     *
     * @return string
     */
    public function __toString()
    {
        return get_class($this);
    }
}
