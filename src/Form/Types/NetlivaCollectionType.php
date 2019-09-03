<?php

namespace Netliva\SymfonyFormBundle\Form\Types;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NetlivaCollectionType extends AbstractType
{

	public function buildView (FormView $view, FormInterface $form, array $options)
	{
		$view->vars['prototype_name'] = $options['prototype_name'];
	}

	public function configureOptions (OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'prototype'		=> true,
			'allow_add'		=> true,
			'allow_delete'	=> true,
			'allow_extra_fields' => true,
			'by_reference'	=> false,
			'attr'			=> ['data-class'=>'collection_type_area'],
		]);
	}

	public function getBlockPrefix ()
	{
		return 'collection_type';
	}

	public function getParent ()
	{
		return CollectionType::class;
	}

}