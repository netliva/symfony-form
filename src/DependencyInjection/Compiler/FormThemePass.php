<?php
namespace Netliva\SymfonyFormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FormThemePass implements CompilerPassInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function process(ContainerBuilder $container): void
	{
		$resources = $container->getParameter('twig.form.resources');

		$formResources = ["@NetlivaSymfonyForm/theme.html.twig"];

		$container->setParameter('twig.form.resources', array_merge($resources, $formResources));
	}
}
