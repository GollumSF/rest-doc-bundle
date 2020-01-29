<?php
namespace Test\GollumSF\RestDocBundle\DependencyInjection;

use GollumSF\RestDocBundle\Configuration\ApiDocConfigurationInterface;
use GollumSF\RestDocBundle\DependencyInjection\GollumSFRestDocExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class GollumSFRestDocExtensionTest extends AbstractExtensionTestCase {

	protected function getContainerExtensions(): array {
		return [
			new GollumSFRestDocExtension()
		];
	}
	
	public function testLoad() {
		$this->load();

		
		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\TypeDiscover\TypeDiscoverInterface::class);
		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\TypeDiscover\Handler\AnnotationHandler::class);
		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\TypeDiscover\Handler\DoctrineHandler::class);
		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\TypeDiscover\Handler\PropertyInfosHandler::class);

		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\Generator\OpenApiGeneratorInterface::class);

		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\Generator\Parameters\ParametersGeneratorInterface::class);
		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\Generator\Parameters\Handler\UrlHandler::class);
		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\Generator\Parameters\Handler\CollectionHandler::class);
		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\Generator\Parameters\Handler\RequestPropertiesHandler::class);

		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\Generator\ResponseProperties\ResponsePropertiesGeneratorInterface::class);
		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\Generator\ResponseProperties\Handler\GroupHandler::class);
		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\Generator\ResponseProperties\Handler\ResponsePropertiesHandler::class);
		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\Generator\ResponseProperties\Handler\CollectionHandler::class);

		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\Generator\RequestBody\RequestBodyGeneratorInterface::class);
		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\Generator\RequestBody\Handler\GroupHandler::class);
		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\Generator\RequestBody\Handler\RequestBodyPropertiesHandler::class);

		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\Generator\Security\SecurityGeneratorInterface::class);
		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\Generator\Security\Handler\QueryParamHandler::class);
		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\Generator\Security\Handler\AuthorizationBearerHandler::class);
		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\Generator\Security\Handler\CustomHandler::class);
		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\Builder\MetadataBuilder\MetadataBuilderInterface::class);

		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\Builder\MetadataBuilder\Handler\AnnotationHandler::class);
		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface::class);
		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\Builder\ModelBuilder\Decorator\PropertyDecorator::class);

		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\Builder\TagBuilder\TagBuilderInterface::class);
		$this->assertContainerBuilderHasService(\GollumSF\RestDocBundle\Builder\TagBuilder\Decorator\AnnotationDecorator::class);
		
		$this->assertContainerBuilderHasService(ApiDocConfigurationInterface::class);
	}

	public function providerLoadConfiguration() {
		return [
			[ [], 'REST Api', '1.0.0', null, [], null, [], null, null, [] ],
			[ [ 'title' => 'TITLE' ], 'TITLE', '1.0.0', null, [], null, [], null, null, [] ],
			[ [ 'version' => 'VERSION' ], 'REST Api', 'VERSION', null, [], null, [], null, null, [] ],
			[ [ 'description' => 'DESCRIPTION' ], 'REST Api', '1.0.0', 'DESCRIPTION', [], null, [], null, null, [] ],
			[ [ 'host' => [ 'domain.com' ]  ], 'REST Api', '1.0.0', null, [ 'domain.com' ], null, [], null, null, [] ],
			[ [ 'default_host' => 'domain.com' ], 'REST Api', '1.0.0', null, [], 'domain.com', [], null, null, [] ],
			[ [ 'protocol' => [ 'http' ] ], 'REST Api', '1.0.0', null, [], null, [ 'http' ], null, null, [] ],
			[ [ 'default_protocol' => 'http' ], 'REST Api', '1.0.0', null, [], null, [], 'http', null, [] ],
			[ [ 'external_docs' => [ 'url' => 'URL', 'description' => 'DESCRIPTION' ] ], 'REST Api', '1.0.0', null, [], null, [], null, [ 'url' => 'URL', 'description' => 'DESCRIPTION' ], [] ],
			[ [ 'security' => [ 'SECURITY' => [ 'type' => 'custom' ] ] ], 'REST Api', '1.0.0', null, [], null, [], null, null, [
				'SECURITY' => [
					'type' => 'custom',
					'defaultValue' => null,
					'name' => null,
					'scheme' => null,
					'data' => null,
				]
			] ],
		];
	}

	/**
	 * @dataProvider providerLoadConfiguration
	 */
	public function testLoadConfiguration(
		$config,
		$title,
		$version,
		$description,
		$host,
		$default_host,
		$protocol,
		$default_protocol,
		$external_docs,
		$security
	) {
		$this->load($config);

		$this->assertContainerBuilderHasServiceDefinitionWithArgument(ApiDocConfigurationInterface::class, 0, $title);
		$this->assertContainerBuilderHasServiceDefinitionWithArgument(ApiDocConfigurationInterface::class, 1, $version);
		$this->assertContainerBuilderHasServiceDefinitionWithArgument(ApiDocConfigurationInterface::class, 2, $description);
		$this->assertContainerBuilderHasServiceDefinitionWithArgument(ApiDocConfigurationInterface::class, 3, $host);
		$this->assertContainerBuilderHasServiceDefinitionWithArgument(ApiDocConfigurationInterface::class, 4, $default_host);
		$this->assertContainerBuilderHasServiceDefinitionWithArgument(ApiDocConfigurationInterface::class, 5, $protocol);
		$this->assertContainerBuilderHasServiceDefinitionWithArgument(ApiDocConfigurationInterface::class, 6, $default_protocol);
		$this->assertContainerBuilderHasServiceDefinitionWithArgument(ApiDocConfigurationInterface::class, 7, $external_docs);
		$this->assertContainerBuilderHasServiceDefinitionWithArgument(ApiDocConfigurationInterface::class, 8, $security);
	}
}