<?php

namespace ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $form = $builder
            ->add('name')
            ->add('birthday')
            ->getForm();

        return $form;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'ShopBundle\Entity\Customer'
        ]);
    }

}