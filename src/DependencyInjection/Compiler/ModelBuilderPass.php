<?php

namespace GollumSF\RestDocBundle\DependencyInjection\Compiler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\MetadataBuilderInterface;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ModelBuilderPass implements CompilerPassInterface
{
	public function process(ContainerBuilder $container)
	{
		// always first check if the primary service is defined
		if (!$container->has(ModelBuilderInterface::class)) {
			return;
		}

		$definition = $container->findDefinition(ModelBuilderInterface::class);

		$taggedServices = $container->findTaggedServiceIds(ModelBuilderInterface::DECORATOR_TAG);
		foreach ($taggedServices as $id => $tags) {
			$definition->addMethodCall('addDecorator', [new Reference($id)]);
		}
	}
}
