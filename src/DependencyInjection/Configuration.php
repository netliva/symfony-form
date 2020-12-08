<?php

namespace Netliva\SymfonyFormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('netliva_symfony_form');


		$rootNode
			->children()

				/* ==== BAĞIMLI ALANLAR ==== */
				->arrayNode('dependent_entities')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
						->isRequired()
                        ->children()
							->arrayNode('static_values')
								->prototype('scalar')->end()
							->end()
							->arrayNode('join')
								->prototype('scalar')->end()
							->end()
                            ->scalarNode('static_only_show_with_results')
                                ->defaultValue(false)
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('em')
                                ->defaultValue('default')
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('class')
                                ->cannotBeEmpty()
								->isRequired()
                            ->end()
                            ->scalarNode('key')
                                ->defaultValue('id')
                                ->cannotBeEmpty()
                            ->end()
							->arrayNode('value')
								->isRequired()
								->prototype('scalar')->end()
								->cannotBeEmpty()
							->end()
                            ->scalarNode('where')
                                ->cannotBeEmpty()
								->isRequired()
                            ->end()
							->arrayNode('other_values')->prototype('scalar')->end()->end()
                            ->scalarNode('role')
                                ->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()



				/* ==== MULTI SELECT ==== */
				->arrayNode('treeselect_entities')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->children()
							->arrayNode('static_values')
								->prototype('scalar')->end()
							->end()
                            ->scalarNode('static_only_show_with_results')
                                ->defaultValue(false)
                            ->end()
                            ->scalarNode('where')
                               ->defaultValue('id')
                            ->end()
                            ->scalarNode('em')
                                ->defaultValue('default')
                            ->end()
                            ->scalarNode('class')
                            ->end()
                            ->scalarNode('key')
                                ->defaultValue('id')
                            ->end()
                            ->scalarNode('value')
                                ->defaultValue('name')
                            ->end()
                            ->scalarNode('value2')
                                ->defaultValue('')
                            ->end()
                            ->scalarNode('role')
                                ->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')
                            ->end()
							->arrayNode('other_values')->prototype('scalar')->end()->end()
                        ->end()
                    ->end()
                ->end()


				/* ==== AJAX AUTO COMPLATE ==== */
				->arrayNode('autocomplete_entities')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
						->prototype('array')
							->children()
								->scalarNode('em')
									->defaultValue('default')
								->end()
								->scalarNode('format')
									->defaultValue(null)
								->end()
								->scalarNode('template')
									->defaultValue(null)
								->end()
								->arrayNode('join')
									->prototype('scalar')->end()
								->end()
								->scalarNode('class')
								->end()
								->scalarNode('key')
									->defaultValue('id')
								->end()
								->arrayNode('value')
									->prototype('scalar')->end()
								->end()
								->variableNode('data')
									->defaultValue(null)
								->end()
								->scalarNode('search')
									->defaultValue('explode_space')
								->end()
								->scalarNode('role')
									->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')
								->end()
								->arrayNode('other_values')->prototype('scalar')->end()->end()
								->scalarNode('search_in_other_values')
									->defaultValue(true)
								->end()
							->end()
						->end()
                    ->end()
                ->end()

			->end();
        return $treeBuilder;
    }
}
