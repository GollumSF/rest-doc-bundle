<?php

namespace GollumSF\RestDocBundle\DependencyInjection\Compiler;

use GollumSF\RestDocBundle\Generator\MetadataBuilder\MetadataBuilderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MetadataBuilderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has(MetadataBuilderInterface::class)) {
            return;
        }

        $definition = $container->findDefinition(MetadataBuilderInterface::class);

        $taggedServices = $container->findTaggedServiceIds(MetadataBuilderInterface::HANDLER_TAG);
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addHandler', [new Reference($id)]);
        }
    }
}
