<?php
namespace Ins\SendGridBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
	public function getConfigTreeBuilder()
	{
		$treeBuilder = new TreeBuilder();
		$treeBuilder
			->root('send_grid')
			->children()
				->scalarNode('apikey')->end()
				->booleanNode('disable_delivery')->defaultFalse()->end()
				->booleanNode('enable_restriction')->defaultFalse()->end()
                ->scalarNode('delivery_address')->end()
                ->arrayNode('patterns')->prototype('scalar')->end()
			->end();

		return $treeBuilder;
	}
}
