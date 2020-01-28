<?php

namespace GollumSF\RestDocBundle\DependencyInjection\Compiler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\MetadataBuilderInterface;
use GollumSF\RestDocBundle\TypeDiscover\TypeDiscoverInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TypeDiscoverPass implements CompilerPassInterface
{
	public function process(ContainerBuilder $container)
	{
		// always first check if the primary service is defined
		if (!$container->has(MetadataBuilderInterface::class)) {
			return;
		}

		$definition = $container->findDefinition(TypeDiscoverInterface::class);

		$taggedServices = $container->findTaggedServiceIds(TypeDiscoverInterface::HANDLER_TAG);
		uasort($taggedServices, function ($a, $b) {
			$aVal = isset($a[0]) && isset($a[0]['priority']) ? $a[0]['priority'] : 0;
			$bVal = isset($b[0]) && isset($b[0]['priority']) ? $b[0]['priority'] : 0;
			if ($aVal === $bVal) {
				return 0;
			}
			return ($aVal < $bVal) ? -1 : 1;
		});
		foreach ($taggedServices as $id => $tags) {
			$definition->addMethodCall('addHandler', [new Reference($id)]);
		}
	}
}
