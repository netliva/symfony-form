<?php

namespace Netliva\SymfonyFormBundle\Form\Types;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
/*
use Symfony\Component\Form\Exception\FormException;
use Netliva\FormBundle\Form\DataTransformer\EntityToPropertyTransformer;
*/
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NetlivaContactType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array());
    }

    public function getBlockPrefix()
    {
        return 'netliva_contact';
    }

    public function getParent()
    {
        return FormType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder->add('type', ChoiceType::class, [
					'label'   => 'Tip',
					'choices' => array_flip([
						'gsm'        => 'Gsm',
						'phone'      => 'Tel',
						'fax'        => 'Faks',
						'email'      => 'E-Posta',
						'glob_gsm'   => 'Global Gsm',
						'glob_phone' => 'Global Tel',
					])
				]
			)->add('content', TextType::class, array("label" => "İçerik"))
			->add('internal', TextType::class, array("label" => "Dahili", 'required' => false))
			->add('notification', CheckboxType::class, array("label" => "Bilgilendirme", 'required' => false))
		;
	}






}
