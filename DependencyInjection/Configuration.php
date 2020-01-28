<?php

namespace GollumSF\RestDocBundle\DependencyInjection;

use GollumSF\RestDocBundle\Generator\Security\Handler\AuthorizationBearerHandler;
use GollumSF\RestDocBundle\Generator\Security\Handler\CustomHandler;
use GollumSF\RestDocBundle\Generator\Security\Handler\QueryParamHandler;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {

	public function getConfigTreeBuilder() {
		
		$treeBuilder = new TreeBuilder('gollum_sf_rest_doc');

		$treeBuilder->getRootNode()->children()
			->scalarNode('title')->defaultValue('REST Api')->end()
			->scalarNode('version')->defaultValue('1.0.0')->end()
			->scalarNode('description')->defaultValue(null)->end()

			->arrayNode('host')
				->defaultValue([])
				->scalarPrototype()->end()
			->end()
			->scalarNode('default_host')->defaultValue(null)->end()
			->arrayNode('protocol')
				->defaultValue([])
				->scalarPrototype()->end()
			->end()
			->scalarNode('default_protocol')->defaultValue(null)->end()
			
			->arrayNode('external_docs')
				->children()
					->scalarNode('url')->isRequired()->end()
					->scalarNode('description')->defaultValue(null)->end()
				->end()
			->end()
			
			->arrayNode('security')
				->defaultValue([])
				->arrayPrototype()
					->children()
						->enumNode('type')->values([
							AuthorizationBearerHandler::SECURITY_TAG,
							QueryParamHandler::SECURITY_TAG,
							CustomHandler::SECURITY_TAG,
						])->isRequired()->end()
						->scalarNode('defaultValue')->defaultValue('')->end()
						->scalarNode('name')->defaultValue(null)->end()
						->scalarNode('scheme')->defaultValue(null)->end()
						->variableNode('data')->defaultValue(null)->end()
					->end()
				->end()
			->end()
		->end();

		return $treeBuilder;
	}
}