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

class PageGadgetType extends AbstractType
{
    public $options = null;

    public function __construct($options = null)
    {
        $this->options = $options;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('point', 'hidden')
            ->add('sortOrder', 'hidden')
            ->add('caption', 'text', array('required' => false))
            ->add('device', 'choice', array(
                'multiple' => true,
                'expanded' => true,
                'choices' => array(
                    'full' => 'pc',
                    'mobile' => 'mobile',
                ),
                'help' => 'If you do not check any, this gadet is available for all.',
            ))
        ;

        foreach ($this->options as $k => $v) {
            $builder->add($k, $v['type'], isset($v['options']) ? $v['options'] : array());
        }

        $builder
            ->add('header', 'textarea', array('required' => false))
            ->add('footer', 'textarea', array('required' => false))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        $options['data_class'] = 'Societo\PageBundle\Entity\PageGadget';

        $options = parent::getDefaultOptions($options);

        return $options;
    }

    public function getName()
    {
        return 'societo_page_page_gadget';
    }
}
