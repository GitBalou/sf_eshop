<?php
/**
 * Created by PhpStorm.
 * User: Gaby
 * Date: 14/04/2017
 * Time: 11:37
 */

namespace ShopBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeoType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title')->add('description');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ShopBundle\Entity\seo'
        ));
    }

    public function getBlockPrefix()
    {
        return 'shopbundle_seo';
    }

}