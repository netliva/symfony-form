<?php

namespace Netliva\SymfonyFormBundle\Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NetlivaCollectionContactType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
			'allow_extra_fields' => true,
			'entry_type'         => NetlivaContactType::class,
			'allow_add'          => true,
			'allow_delete'       => true,
			'prototype_name'     => '__contact_name__',
			'by_reference'       => false,
			'js_settings'        => ["addBtnText" => "İletişim Bilgisi Ekle"],
			'label'              => 'Yeni bir iletişim bilgisi eklemek için Ekle butonunu kullanınız.'
		));
    }

    public function getBlockPrefix(): string
    {
        return 'netliva_collection_contact';
    }

    public function getParent(): ?string
    {
        return NetlivaCollectionType::class;
    }
}
