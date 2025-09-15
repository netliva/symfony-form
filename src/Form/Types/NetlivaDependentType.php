<?php

namespace Netliva\SymfonyFormBundle\Form\Types;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Netliva\SymfonyFormBundle\Form\DataTransformer\EntityToIdTransformer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NetlivaDependentType extends AbstractType
{
	public function __construct (
        private readonly ContainerInterface $container,
        private readonly ManagerRegistry $registry
    ) {
	}

	public function getBlockPrefix(): string	{
		return 'netliva_dependent';
	}

	public function getParent(): string
	{
		return TextType::class;
	}

	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		if ($options['transformer'])
		{
			$depentConfig = $this->container->getParameter('netliva_form.dependent_entities');
			$builder->addModelTransformer(new EntityToIdTransformer($options['em'], $depentConfig[$options['entity_alias']]));
		}

		$builder->setAttribute('entity_alias', $options['entity_alias']);
		$builder->setAttribute('depend_to', $options['depend_to']);
	}

	public function buildView (FormView $view, FormInterface $form, array $options): void
	{

		$view->vars['default']      = $options['default'];
		$view->vars['entity_alias'] = $options['entity_alias'];
		$view->vars['depend_to']    = $options['depend_to'];


	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$emNormalizer = function(Options $options, $em) {
			if (null !== $em)
			{
				if ($em instanceof ObjectManager)
				{
					return $em;
				}

				return $this->registry->getManager($em);
			}

			$depentConfig = $this->container->getParameter('netliva_form.dependent_entities');
			$em = $this->registry->getManagerForClass($depentConfig[$options['entity_alias']]['class']);

			if (null === $em)
			{
				throw new RuntimeException(
					sprintf(
						'Class "%s" seems not to be a managed Doctrine entity. Did you forget to map it?',
						$options['class']
					)
				);
			}

			return $em;
		};


		$resolver->setDefaults(
			[
				'default'     => 'Bir değer seçiniz',
				'transformer' => true,
				'em'          => null,
			]
		);

		$resolver->setNormalizer('em', $emNormalizer);
		$resolver->setRequired(['depend_to', 'entity_alias']);
		$resolver->setAllowedTypes('em', ['null', 'string', ObjectManager::class, EntityManagerInterface::class]);

	}

}
