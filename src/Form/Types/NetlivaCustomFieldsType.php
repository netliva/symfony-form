<?php

namespace Netliva\SymfonyFormBundle\Form\Types;

use Netliva\FileTypeBundle\Form\Type\NetlivaFileType;
use Netliva\SymfonyFormBundle\Services\NetlivaCustomFieldsExtension;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NetlivaCustomFieldsType extends AbstractType
{
    private $ncfe;
    public function __construct (
        NetlivaCustomFieldsExtension $ncfe
    ) {
        $this->ncfe = $ncfe;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
		   'fields'       => [],
        ));
    }

    public function getBlockPrefix(): string
    {
        return 'netliva_customfield';
    }

    public function getParent()
    {
        return FormType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
		uasort($options["fields"], function($a, $b) {
			return (key_exists("order", $a) ? $a['order'] : 0) <=> (key_exists("order", $b) ? $b['order'] : 0);
		});

		foreach ($options["fields"] as $key => $field)
		{
			[$type, $opt] = $this->ncfe->prepareFieldOptions($field);
			$builder->add($key, $type, $opt);
		}
    }


    public function buildView(FormView $view, FormInterface $form, array $options)
    {

	}
}

