<?php
namespace GollumSF\RestDocBundle\DependencyInjection\Compiler;

use Doctrine\Persistence\ManagerRegistry;
use GollumSF\RestDocBundle\TypeDiscover\Handler\DoctrineHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DoctrineBuilderPass implements CompilerPassInterface
{
	public function process(ContainerBuilder $container)
	{
		if (!$container->hasDefinition(ManagerRegistry::class) && !$container->hasAlias(ManagerRegistry::class)) {
			return;
		}
		$container->getDefinition(DoctrineHandler::class)->addMethodCall('setManagerRegistry', [ new Reference(ManagerRegistry::class) ]);
	}
}
