<?php

/**
 * SocietoPageBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\PageBundle\Entity;

use Societo\BaseBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Societo\PageBundle\Repository\PageGadgetRepository")
 * @ORM\Table(name="page_gadget")
 */
class PageGadget extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="Page")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     */
    private $page;

    /**
     * @ORM\Column(name="point", type="string")
     */
    private $point;

    /**
     * @ORM\Column(name="sort_order", type="integer", nullable=true)
     */
    private $sortOrder = 0;

    /**
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @ORM\Column(name="caption", type="text")
     */
    private $caption = '';

    /**
     * @ORM\Column(name="parameters", type="array")
     */
    private $parameters;

    public function __construct($page, $point, $name, $caption = '', $parameters = array())
    {
        $this->page = $page;
        $this->point = $point;
        $this->name = $name;
        $this->caption = $caption;
        $this->parameters = $parameters;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCaption()
    {
        return $this->caption;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getParameter($name, $default = null)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : $default;
    }

    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    public function getPoint()
    {
        return $this->point;
    }

    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    public function setSortOrder($order)
    {
        $this->sortOrder = $order;
    }

    public function setPoint($point)
    {
        $this->point = $point;
    }

    public function setCaption($caption = '')
    {
        $this->caption = (string)$caption;
    }

    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function __get($name)
    {
        if (isset($this->parameters[$name])) {
            return $this->parameters[$name];
        }

        return null;
    }

    public function __set($name, $value)
    {
        $this->parameters[$name] = $value;
    }
}
