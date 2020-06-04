<?php

namespace Netliva\SymfonyFormBundle\Form\Types;

use Netliva\SymfonyFormBundle\Form\Types\NetlivaContactType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NetlivaCollectionContactType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
		   'allow_extra_fields' => true,
		   'entry_type'         => NetlivaContactType::class,
		   'allow_add'          => true,
		   'allow_delete'       => true,
		   'prototype_name'     => '__contact_name__',
		   'by_reference'       => false,
		   'label'              => 'Yeni bir iletişim bilgisi eklemek için Ekle butonunu kullanınız.'
        ));
    }

    public function getBlockPrefix()
    {
        return 'netliva_collection_contact';
    }

    public function getParent()
    {
        return NetlivaCollectionType::class;
    }
}
