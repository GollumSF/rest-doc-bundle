<?php

namespace GollumSF\RestDocBundle\DependencyInjection\Compiler;

use GollumSF\RestDocBundle\Generator\Parameters\ParametersGeneratorInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ParametersGeneratorPass implements CompilerPassInterface
{
	public function process(ContainerBuilder $container)
	{
		// always first check if the primary service is defined
		if (!$container->has(ParametersGeneratorInterface::class)) {
			return;
		}

		$definition = $container->findDefinition(ParametersGeneratorInterface::class);

		$taggedServices = $container->findTaggedServiceIds(ParametersGeneratorInterface::HANDLER_TAG);
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
