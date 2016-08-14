<?php

namespace Dywee\OrderCMSBundle\Form;

use Dywee\AddressBundle\Form\CompleteAddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillingAddressType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
