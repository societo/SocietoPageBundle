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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="page")
 */
class Page extends BaseEntity
{
    /**
     * @ORM\Column(name="name", type="string")
     * @Assert\NotNull
     * @Assert\Regex(pattern="/^[a-zA-Z0-9_]+$/u")
     */
    private $name;

    /**
     * @ORM\Column(name="path", type="string")
     */
    private $path = '';

    /**
     * @ORM\Column(name="title", type="string")
     */
    private $title = '';

    /**
     * @ORM\Column(name="parameters", type="array")
     */
    private $parameters;

    /**
     * @ORM\Column(name="is_published", type="boolean")
     */
    private $published = true;

    /**
     * @ORM\OneToMany(targetEntity="PageGadget", mappedBy="page")
     */
    private $gadgets;

    public function __construct($name, $path = '', $parameters = array())
    {
        $this->name = $name;
        $this->path = $path;
        $this->parameters = $parameters;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = (string)$path;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getTitle()
    {
        if ($this->title) {
            return $this->title;
        }

        return $this->getName();
    }

    public function setTitle($title)
    {
        $this->title = (string)$title;
    }

    public function getGadgets()
    {
        return $this->gadgets;
    }

    public function setPublished($bool = false)
    {
        $this->published = (bool)$bool;
    }

    public function getPublished()
    {
        return $this->published;
    }

    public function isAnonymousAccess()
    {
        return isset($this->parameters['anonymous_access']) ? $this->parameters['anonymous_access'] : false;
    }

    public function getParameters()
    {
        return $this->parameters = array();
    }

    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    public function __set($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    public function __get($name)
    {
        if (isset($this->parameters[$name]))
        {
            return $this->parameters[$name];
        }

        return null;
    }
}
