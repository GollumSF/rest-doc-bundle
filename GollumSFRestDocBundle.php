<?php
namespace GollumSF\RestDocBundle;

use GollumSF\RestDocBundle\DependencyInjection\Compiler\MetadataBuilderPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\ModelBuilderPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\TagBuilderPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * GollumSFRestBundle
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class GollumSFRestDocBundle extends Bundle {
	
	public function build(ContainerBuilder $container) {
		$container->addCompilerPass(new MetadataBuilderPass());
		$container->addCompilerPass(new TagBuilderPass());
		$container->addCompilerPass(new ModelBuilderPass());
	}
}
