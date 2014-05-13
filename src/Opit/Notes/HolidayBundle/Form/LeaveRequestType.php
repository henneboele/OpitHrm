<?php

/*
 * The MIT License
 *
 * Copyright 2014 Marton Kaufmann <kaufmann@opit.hu>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Opit\Notes\HolidayBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Opit\Notes\HolidayBundle\Form\LeaveType;
use Opit\Notes\HolidayBundle\Form\DataTransformer\EmployeeIdToObjectTransformer;

/**
 * Description of HolidyaRequestType
 *
 * @author OPIT Consulting Kft. - PHP Team - {@link http://www.opit.hu}
 * @package Opit
 * @subpackage Notes
 */
class LeaveRequestType extends AbstractType
{
    private $isNewLeaveRequest;
    
    public function __construct($isNewLeaveRequest)
    {
        $this->isNewLeaveRequest = $isNewLeaveRequest;
    }
    
     /**
     * Builds a form with given fields.
     *
     * @param object  $builder A Formbuilder interface object
     * @param array   $options An array of options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entityManager = $options['em'];
        $transformer = new EmployeeIdToObjectTransformer($entityManager);
        $builder->add($builder->create('employee', 'hidden')->addModelTransformer($transformer));
        
        $builder->add('leaves', 'collection', array(
            'type'         => new LeaveType(),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false
        ));
        
        $builder->add('create_leave_request', 'submit', array(
            'label' => $this->isNewLeaveRequest ? 'Add leave request' : 'Edit leave request',
            'attr' => array('class' => 'button')
        ));
    }

   /**
     * Sets the default form options
     *
     * @param object $resolver An OptionsResolver interface object
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Opit\Notes\HolidayBundle\Entity\LeaveRequest',
            'validation_groups' => array('user')
        ))
        ->setRequired(array(
            'em',
        ))
        ->setAllowedTypes(array(
            'em' => 'Doctrine\Common\Persistence\ObjectManager',
        ));
    }
    /**
     * Get the name
     *
     * @return string name
     */
    public function getName()
    {
        return 'leave_request';
    }
}