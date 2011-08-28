<?php

/**
 * SocietoPageBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\PageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityManager;

class PageType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('name', 'text', array('required' => false))
            ->add('path', 'text', array('required' => false))
            ->add('title', 'text', array('required' => false))
            ->add('published', 'checkbox', array('required' => false))
            ->add('anonymous_access', 'checkbox', array('required' => false))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        $options['data_class'] = 'Societo\PageBundle\Entity\Page';

        $options = parent::getDefaultOptions($options);

        return $options;
    }

    public function getName()
    {
        return 'societo_page_page';
    }
}
