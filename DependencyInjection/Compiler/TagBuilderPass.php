<?php

namespace GollumSF\RestDocBundle\DependencyInjection\Compiler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\MetadataBuilderInterface;
use GollumSF\RestDocBundle\Builder\TagBuilder\TagBuilderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TagBuilderPass implements CompilerPassInterface
{
	public function process(ContainerBuilder $container)
	{
		// always first check if the primary service is defined
		if (!$container->has(MetadataBuilderInterface::class)) {
			return;
		}

		$definition = $container->findDefinition(TagBuilderInterface::class);

		$taggedServices = $container->findTaggedServiceIds(TagBuilderInterface::DECORATOR_TAG);
		foreach ($taggedServices as $id => $tags) {
			$definition->addMethodCall('addDecorator', [new Reference($id)]);
		}
	}
}
