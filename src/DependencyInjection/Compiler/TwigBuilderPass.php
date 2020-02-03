<?php
namespace GollumSF\RestDocBundle\DependencyInjection\Compiler;

use GollumSF\RestDocBundle\Controller\SwaggerUIController;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Twig\Environment;

class TwigBuilderPass implements CompilerPassInterface
{
	public function process(ContainerBuilder $container)
	{
		if (!$container->hasDefinition(Environment::class) && !$container->hasAlias(Environment::class)) {
			return;
		}
		$container->getDefinition(SwaggerUIController::class)->addMethodCall('setTwig', [ new Reference(Environment::class) ]);
	}
}
