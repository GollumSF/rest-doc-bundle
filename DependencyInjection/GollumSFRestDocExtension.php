<?php
namespace GollumSF\RestDocBundle\DependencyInjection;

use GollumSF\RestDocBundle\Configuration\ApiDocConfiguration;
use GollumSF\RestDocBundle\Configuration\ApiDocConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class GollumSFRestDocExtension extends Extension
{
	public function load(array $configs, ContainerBuilder $container)
	{
		$loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
		$loader->load('services.yml');

		$configuration = new Configuration();
		$config = $this->processConfiguration($configuration, $configs);

		$definition = $container->register(ApiDocConfigurationInterface::class, ApiDocConfiguration::class);
		$definition->addArgument($config['title']);
		$definition->addArgument($config['version']);
		$definition->addArgument($config['description']);
		$definition->addArgument($config['host']);
		$definition->addArgument($config['default_host']);
		$definition->addArgument($config['protocol']);
		$definition->addArgument($config['default_protocol']);
		$definition->addArgument(isset($config['external_docs']) ? $config['external_docs'] : null);
	}
}