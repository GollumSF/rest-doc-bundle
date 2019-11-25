<?php
namespace GollumSF\RestDocBundle;

use GollumSF\RestDocBundle\DependencyInjection\Compiler\MetadataFactoryPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * GollumSFRestBundle
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class GollumSFRestDocBundle extends Bundle {
	
	public function build(ContainerBuilder $container) {
		$container->addCompilerPass(new MetadataFactoryPass());
	}
}
