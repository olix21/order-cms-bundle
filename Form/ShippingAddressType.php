<?php

namespace Dywee\OrderCMSBundle\Form;

use Dywee\AddressBundle\Form\CompleteAddressType;
use Dywee\OrderCMSBundle\Form\AddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShippingAddressType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shippingMessage',    TextareaType::class,   array('label' => 'address.form.shippingMessage', 'required' => false, 'mapped' => false))
            ->add('other',              TextType::class,       array('label' => 'address.form.other', 'required' => false))
        ;
    }

    public function getParent()
    {
        return CompleteAddressType::class;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Dywee\AddressBundle\Entity\Address'
        ));
    }
}
