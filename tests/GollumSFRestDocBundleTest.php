<?php
namespace Test\GollumSF\RestDocBundle;

use Doctrine\Persistence\ManagerRegistry;
use GollumSF\ControllerActionExtractorBundle\GollumSFControllerActionExtractorBundle;
use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestBundle\GollumSFRestBundle;
use GollumSF\RestDocBundle\Configuration\ApiDocConfiguration;
use GollumSF\RestDocBundle\Configuration\ApiDocConfigurationInterface;
use GollumSF\RestDocBundle\Controller\SwaggerUIController;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\MetadataBuilderPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\ModelBuilderPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\ParametersGeneratorPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\RequestBodyGeneratorPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\ResponsePropertiesGeneratorPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\SecurityGeneratorPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\TagBuilderPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\TypeDiscoverPass;
use GollumSF\RestDocBundle\GollumSFRestDocBundle;
use GollumSF\RestDocBundle\TypeDiscover\Handler\DoctrineHandler;
use Nyholm\BundleTest\BaseBundleTestCase;
use Nyholm\BundleTest\CompilerPass\PublicServicePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Twig\Environment;

class GollumSFRestDocBundleTest extends BaseBundleTestCase {

	use ReflectionPropertyTrait;

	protected function getBundleClass() {
		return GollumSFRestDocBundle::class;
	}
	protected function setUp(): void {
		parent::setUp();

		// Make all services public
		$this->addCompilerPass(new PublicServicePass('|GollumSF*|'));
	}

	public function testInitBundle() {

		// Create a new Kernel
		$kernel = $this->createKernel();

		// Add some configuration
		$kernel->addConfigFile(__DIR__.'/Resources/config.yaml');
		
		// Boot the kernel.
		$this->bootKernel();

		// Get the container
		$container = $this->getContainer();

		$this->validAllServices($container);
	}


	public function testInitBundleGSContainerActionExtractor() {

		// Create a new Kernel
		$kernel = $this->createKernel();

		// Add some other bundles we depend on
		$kernel->addBundle(\GollumSF\ControllerActionExtractorBundle\GollumSFControllerActionExtractorBundle::class);

		// Add some configuration
		$kernel->addConfigFile(__DIR__.'/Resources/config.yaml');

		// Boot the kernel.
		$this->bootKernel();

		// Get the container
		$container = $this->getContainer();

		$this->validAllServices($container);
	}

	/**
	 * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
	 */
	protected function validAllServices(\Symfony\Component\DependencyInjection\ContainerInterface $container): void
	{
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Controller\OpenApiController::class, $container->get(\GollumSF\RestDocBundle\Controller\OpenApiController::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Controller\SwaggerUIController::class, $container->get(\GollumSF\RestDocBundle\Controller\SwaggerUIController::class));

		$this->assertInstanceOf(\GollumSF\RestDocBundle\TypeDiscover\TypeDiscoverInterface::class, $container->get(\GollumSF\RestDocBundle\TypeDiscover\TypeDiscoverInterface::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\TypeDiscover\TypeDiscover::class, $container->get(\GollumSF\RestDocBundle\TypeDiscover\TypeDiscoverInterface::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\TypeDiscover\Handler\AnnotationHandler::class, $container->get(\GollumSF\RestDocBundle\TypeDiscover\Handler\AnnotationHandler::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\TypeDiscover\Handler\DoctrineHandler::class, $container->get(\GollumSF\RestDocBundle\TypeDiscover\Handler\DoctrineHandler::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\TypeDiscover\Handler\PropertyInfosHandler::class, $container->get(\GollumSF\RestDocBundle\TypeDiscover\Handler\PropertyInfosHandler::class));

		$this->assertInstanceOf(\GollumSF\RestDocBundle\Generator\OpenApiGeneratorInterface::class, $container->get(\GollumSF\RestDocBundle\Generator\OpenApiGeneratorInterface::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Generator\OpenApiGenerator::class, $container->get(\GollumSF\RestDocBundle\Generator\OpenApiGeneratorInterface::class));

		$this->assertInstanceOf(\GollumSF\RestDocBundle\Generator\Parameters\ParametersGeneratorInterface::class, $container->get(\GollumSF\RestDocBundle\Generator\Parameters\ParametersGeneratorInterface::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Generator\Parameters\ParametersGenerator::class, $container->get(\GollumSF\RestDocBundle\Generator\Parameters\ParametersGeneratorInterface::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Generator\Parameters\Handler\UrlHandler::class, $container->get(\GollumSF\RestDocBundle\Generator\Parameters\Handler\UrlHandler::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Generator\Parameters\Handler\CollectionHandler::class, $container->get(\GollumSF\RestDocBundle\Generator\Parameters\Handler\CollectionHandler::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Generator\Parameters\Handler\RequestPropertiesHandler::class, $container->get(\GollumSF\RestDocBundle\Generator\Parameters\Handler\RequestPropertiesHandler::class));

		$this->assertInstanceOf(\GollumSF\RestDocBundle\Generator\ResponseBody\ResponseBodyGeneratorInterface::class, $container->get(\GollumSF\RestDocBundle\Generator\ResponseBody\ResponseBodyGeneratorInterface::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Generator\ResponseBody\ResponseBodyGenerator::class, $container->get(\GollumSF\RestDocBundle\Generator\ResponseBody\ResponseBodyGeneratorInterface::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Generator\ResponseBody\Handler\GroupHandler::class, $container->get(\GollumSF\RestDocBundle\Generator\ResponseBody\Handler\GroupHandler::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Generator\ResponseBody\Handler\ResponseBodyPropertiesHandler::class, $container->get(\GollumSF\RestDocBundle\Generator\ResponseBody\Handler\ResponseBodyPropertiesHandler::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Generator\ResponseBody\Handler\CollectionHandler::class, $container->get(\GollumSF\RestDocBundle\Generator\ResponseBody\Handler\CollectionHandler::class));

		$this->assertInstanceOf(\GollumSF\RestDocBundle\Generator\RequestBody\RequestBodyGeneratorInterface::class, $container->get(\GollumSF\RestDocBundle\Generator\RequestBody\RequestBodyGeneratorInterface::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Generator\RequestBody\RequestBodyGenerator::class, $container->get(\GollumSF\RestDocBundle\Generator\RequestBody\RequestBodyGeneratorInterface::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Generator\RequestBody\Handler\GroupHandler::class, $container->get(\GollumSF\RestDocBundle\Generator\RequestBody\Handler\GroupHandler::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Generator\RequestBody\Handler\RequestBodyPropertiesHandler::class, $container->get(\GollumSF\RestDocBundle\Generator\RequestBody\Handler\RequestBodyPropertiesHandler::class));

		$this->assertInstanceOf(\GollumSF\RestDocBundle\Generator\Security\SecurityGeneratorInterface::class, $container->get(\GollumSF\RestDocBundle\Generator\Security\SecurityGeneratorInterface::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Generator\Security\SecurityGenerator::class, $container->get(\GollumSF\RestDocBundle\Generator\Security\SecurityGeneratorInterface::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Generator\Security\Handler\QueryParamHandler::class, $container->get(\GollumSF\RestDocBundle\Generator\Security\Handler\QueryParamHandler::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Generator\Security\Handler\AuthorizationBearerHandler::class, $container->get(\GollumSF\RestDocBundle\Generator\Security\Handler\AuthorizationBearerHandler::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Generator\Security\Handler\CustomHandler::class, $container->get(\GollumSF\RestDocBundle\Generator\Security\Handler\CustomHandler::class));

		$this->assertInstanceOf(\GollumSF\RestDocBundle\Builder\MetadataBuilder\MetadataBuilderInterface::class, $container->get(\GollumSF\RestDocBundle\Builder\MetadataBuilder\MetadataBuilderInterface::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Builder\MetadataBuilder\MetadataBuilder::class, $container->get(\GollumSF\RestDocBundle\Builder\MetadataBuilder\MetadataBuilderInterface::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Builder\MetadataBuilder\Handler\AnnotationHandler::class, $container->get(\GollumSF\RestDocBundle\Builder\MetadataBuilder\Handler\AnnotationHandler::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface::class, $container->get(\GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Builder\ModelBuilder\Decorator\PropertyDecorator::class, $container->get(\GollumSF\RestDocBundle\Builder\ModelBuilder\Decorator\PropertyDecorator::class));

		$this->assertInstanceOf(\GollumSF\RestDocBundle\Builder\TagBuilder\TagBuilderInterface::class, $container->get(\GollumSF\RestDocBundle\Builder\TagBuilder\TagBuilderInterface::class));
		$this->assertInstanceOf(\GollumSF\RestDocBundle\Builder\TagBuilder\Decorator\AnnotationDecorator::class, $container->get(\GollumSF\RestDocBundle\Builder\TagBuilder\Decorator\AnnotationDecorator::class));

		$this->assertInstanceOf(ApiDocConfigurationInterface::class, $container->get(ApiDocConfigurationInterface::class));
		$this->assertInstanceOf(ApiDocConfiguration::class, $container->get(ApiDocConfigurationInterface::class));

		$this->assertNull($this->reflectionGetValue($container->get(DoctrineHandler::class), 'managerRegistry'));
		$this->assertNull($this->reflectionGetValue($container->get(SwaggerUIController::class), 'twig'));
	}

	public function testInitBundleWithDoctrine() {

		// Create a new Kernel
		$kernel = $this->createKernel();

		// Add some other bundles we depend on
		$kernel->addBundle(\Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class);

		// Add some configuration
		$kernel->addConfigFile(__DIR__.'/Resources/config_doctrine.yaml');

		// Boot the kernel.
		$this->bootKernel();

		// Get the container
		$container = $this->getContainer();

		$this->assertInstanceOf(ManagerRegistry::class, $this->reflectionGetValue($container->get(DoctrineHandler::class), 'managerRegistry'));
	}

	public function testInitBundleWithTwig() {

		// Create a new Kernel
		$kernel = $this->createKernel();

		// Add some other bundles we depend on
		$kernel->addBundle(\Symfony\Bundle\TwigBundle\TwigBundle::class);

		// Add some configuration
		$kernel->addConfigFile(__DIR__.'/Resources/config_twig.yaml');

		// Boot the kernel.
		$this->bootKernel();

		// Get the container
		$container = $this->getContainer();

		$this->assertInstanceOf(Environment::class, $this->reflectionGetValue($container->get(SwaggerUIController::class), 'twig'));
	}
}
