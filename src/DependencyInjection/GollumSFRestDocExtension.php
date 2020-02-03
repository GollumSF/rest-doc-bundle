<?php
namespace GollumSF\RestDocBundle\DependencyInjection;

use GollumSF\ControllerActionExtractorBundle\DependencyInjection\GollumSFControllerActionExtractorExtension;
use GollumSF\ControllerActionExtractorBundle\Extractor\ControllerActionExtractorInterface;
use GollumSF\RestDocBundle\Configuration\ApiDocConfiguration;
use GollumSF\RestDocBundle\Configuration\ApiDocConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class GollumSFRestDocExtension extends Extension {
	
	public function load(array $configs, ContainerBuilder $container) {

		$bundles = $container->getParameter('kernel.bundles');
		if (!isset($bundles['GollumSFControllerActionExtractorBundle'])) {
			if (!$container->hasDefinition(ControllerActionExtractorInterface::class)) {
				$extension = new GollumSFControllerActionExtractorExtension();
				$extension->load([], $container);
			}
		}
		
		$loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
		$loader->load('services.yml');
		$config = $this->processConfiguration(new Configuration(), $configs);

		$container
			->register(ApiDocConfigurationInterface::class, ApiDocConfiguration::class)
			->addArgument($config['title'])
			->addArgument($config['version'])
			->addArgument($config['description'])
			->addArgument($config['host'])
			->addArgument($config['default_host'])
			->addArgument($config['protocol'])
			->addArgument($config['default_protocol'])
			->addArgument(isset($config['external_docs']) ? $config['external_docs'] : null)
			->addArgument($config['security'])
		;
	}
}