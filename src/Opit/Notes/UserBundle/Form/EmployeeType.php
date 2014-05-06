<?php

/*
 *  This file is part of the {Bundle}.
 * 
 *  (c) Opit Consulting Kft. <info@opit.hu>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Opit\Notes\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of EmployeeType
 *
 * @author OPIT Consulting Kft. - PHP Team - {@link http://www.opit.hu}
 * @version 1.0
 * @package Notes
 * @subpackage UserBundle
 */
class EmployeeType extends AbstractType
{
    /**
     * Builds a form with given fields.
     *
     * @param object  $builder A Formbuilder interface object
     * @param array   $options An array of options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('teams', 'entity', array(
            'label' => 'Teams',
            'class' => 'OpitNotesUserBundle:Team',
            'property' => 'teamName',
            'multiple' => true,
            'expanded' => true
        ));
        
        $builder->add('numberOfChildren', 'number', array('attr' => array(
            'placeholder' => 'Number of children',
            'min' => '0',
            'max' => '30'
        )));
        
        $builder->add('joiningDate', 'date', array(
            'widget' => 'single_text',
            'attr' => array(
                'placeholder' => 'Joining date'
            )
        ));
        
        $builder->add('dateOfBirth', 'date', array(
            'widget' => 'single_text',
            'attr' => array(
                'placeholder' => 'Date of birth'
            )
        ));
    }
    
    /**
     * Sets the default form options
     * 
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Opit\Notes\UserBundle\Entity\Employee'
        ));
    }

    /**
     * Get name
     * 
     * @return string
     */
    public function getName()
    {
        return 'employee';
    }
}