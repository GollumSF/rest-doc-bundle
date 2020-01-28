<?php

namespace GollumSF\RestDocBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {

	public function getConfigTreeBuilder() {
		
		$treeBuilder = new TreeBuilder('gollum_sf_rest_doc');

		$treeBuilder->getRootNode()->children()
			->scalarNode('title')->defaultValue('REST Api')->end()
			->scalarNode('version')->defaultValue('1.0.0')->end()
			->scalarNode('description')->defaultValue(null)->end()
			
			->arrayNode('external_docs')->children()
				->scalarNode('url')->isRequired()->end()
				->scalarNode('description')->defaultValue(null)->end()
			->end()
		->end();

		return $treeBuilder;
	}
}