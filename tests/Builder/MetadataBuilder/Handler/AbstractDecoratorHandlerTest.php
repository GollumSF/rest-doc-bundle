<?php

namespace Test\GollumSF\RestDocBundle\Builder\MetadataBuilder\Handler;

use GollumSF\ControllerActionExtractorBundle\Extractor\ControllerAction;
use GollumSF\ControllerActionExtractorBundle\Extractor\ControllerActionExtractorInterface;
use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestBundle\Metadata\Serialize\MetadataSerialize;
use GollumSF\RestBundle\Metadata\Serialize\MetadataSerializeManagerInterface;
use GollumSF\RestBundle\Metadata\Unserialize\MetadataUnserialize;
use GollumSF\RestBundle\Metadata\Unserialize\MetadataUnserializeManagerInterface;
use GollumSF\RestDocBundle\Annotation\ApiDescribe;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\Handler\AbstractDecoratorHandler;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class AbstractDecoratorHandlerGetMetadataCollection extends AbstractDecoratorHandler {
	
	private $metadatas;
	
	public $routes = [];
	public $controllerAction = [];
	
	public function __construct(
		RouterInterface $router,
		ControllerActionExtractorInterface $controllerActionExtractor,
		MetadataSerializeManagerInterface $metadataSerializeManager,
		MetadataUnserializeManagerInterface $metadataUnserializeManager,
		array $metadatas
	) {
		parent::__construct(
			$router,
			$controllerActionExtractor,
			$metadataSerializeManager,
			$metadataUnserializeManager
		);
		$this->metadatas = $metadatas;
	}
	
	protected function createMetadata(Route $route, ControllerAction $controllerAction): ?Metadata {
		$this->routes[] = $route;
		$this->controllerAction[] = $controllerAction;
		return array_pop($this->metadatas);
	}
	
	protected function getClassDecorator(\ReflectionClass $rClass): ?ApiDescribe {
		return null;
	}
	
	protected function getMethodDecorator(\ReflectionMethod $rMethod): ?ApiDescribe {
		return null;
	}
}


class AbstractDecoratorHandlerMockAbstract extends AbstractDecoratorHandler {
	
	/** @var AbstractDecoratorHandler */
	private $mock;
	
	public $routes = [];
	public $controllerAction = [];
	
	public function __construct(
		RouterInterface $router,
		ControllerActionExtractorInterface $controllerActionExtractor,
		MetadataSerializeManagerInterface $metadataSerializeManager,
		MetadataUnserializeManagerInterface $metadataUnserializeManager,
		AbstractDecoratorHandler $mock
	) {
		parent::__construct(
			$router,
			$controllerActionExtractor,
			$metadataSerializeManager,
			$metadataUnserializeManager
		);
		$this->mock = $mock;
	}
	
	protected function getClassDecorator(\ReflectionClass $rClass): ?ApiDescribe {
		return $this->mock->getClassDecorator($rClass);
	}
	
	protected function getMethodDecorator(\ReflectionMethod $rMethod): ?ApiDescribe {
		return $this->mock->getMethodDecorator($rMethod);
	}
}

class DummyController {
	public function dummyAction() {
	}
}

class AbstractDecoratorHandlerTest extends TestCase {
	
	use ReflectionPropertyTrait;
	
	public function testGetMetadataCollection() {

		$router                     = $this->getMockForAbstractClass(RouterInterface::class);
		$controllerActionExtractor  = $this->getMockForAbstractClass(ControllerActionExtractorInterface::class);
		$metadataSerializeManager   = $this->getMockForAbstractClass(MetadataSerializeManagerInterface::class);
		$metadataUnserializeManager = $this->getMockForAbstractClass(MetadataUnserializeManagerInterface::class);

		$metadata1 = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata2 = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata3 = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();

		$route1 = $this->getMockBuilder(Route::class)->disableOriginalConstructor()->getMock();
		$route2 = $this->getMockBuilder(Route::class)->disableOriginalConstructor()->getMock();
		$route3 = $this->getMockBuilder(Route::class)->disableOriginalConstructor()->getMock();

		$routeCollection = new RouteCollection();
		$routeCollection->add('route1', $route1);
		$routeCollection->add('route2', $route2);
		$routeCollection->add('route3', $route3);

		$controllerAction1 = new ControllerAction('controller1', 'action1');
		$controllerAction2 = new ControllerAction('controller2', 'action2');
		$controllerAction3 = new ControllerAction('controller3', 'action3');
		
		
		$router
			->method('getRouteCollection')
			->willReturn($routeCollection)
		;

		$controllerActionExtractor
			->expects($this->exactly(3))
			->method('extractFromRoute')
			->withConsecutive(
				[ $route1 ],
				[ $route2 ],
				[ $route3 ]
			)
			->willReturnOnConsecutiveCalls(
				$controllerAction1,
				$controllerAction2,
				$controllerAction3
			)
		;
		
		$handler = new AbstractDecoratorHandlerGetMetadataCollection(
			$router,
			$controllerActionExtractor,
			$metadataSerializeManager,
			$metadataUnserializeManager,
			[
				$metadata1,
				$metadata2,
				$metadata3
			]
		);
		
		$collection = $handler->getMetadataCollection();
		
		$this->assertEquals($collection, [
			$metadata1,
			$metadata2,
			$metadata3
		]);

		$this->assertEquals($handler->routes, [
			$route1,
			$route2,
			$route3
		]);

		$this->assertEquals($handler->controllerAction, [
			new ControllerAction('controller1', 'action1'),
			new ControllerAction('controller2', 'action2'),
			new ControllerAction('controller3', 'action3'),
		]);
	}
	
	public function providerCreateMatadata() {
		return [
			
			// Entity Class
			
			[ new ApiDescribe('EntityClass1'), null, null, null, 'EntityClass1', false, [], [], [], [] ],
			[ null, new ApiDescribe('EntityClass1'), null, null, 'EntityClass1', false, [], [], [], [] ],
			[ new ApiDescribe('EntityClass1'), new ApiDescribe('EntityClass2'), null, null, 'EntityClass2', false, [], [], [], [] ],

			[ new ApiDescribe('EntityClass1', true), null, null, null, 'EntityClass1', true, [], [], [], [] ],
			[ null, new ApiDescribe('EntityClass1', true), null, null, 'EntityClass1', true, [], [], [], [] ],
			[ new ApiDescribe('EntityClass1', true), new ApiDescribe('EntityClass2', false), null, null, 'EntityClass2', false, [], [], [], [] ],
			[ new ApiDescribe('EntityClass1', false), new ApiDescribe('EntityClass2', true), null, null, 'EntityClass2', true, [], [], [], [] ],
			
			// Describe Serialize group

			[ new ApiDescribe('EntityClass1', false, [ 'group1' ]), null, null, null, 'EntityClass1', false, [ 'group1' ], [], [], [] ],
			[ null, new ApiDescribe('EntityClass1', false, [ 'group1' ]), null, null, 'EntityClass1', false, [ 'group1' ], [], [], [] ],
			[ new ApiDescribe('EntityClass1', false, [ 'group1' ]), new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), null, null, 'EntityClass1', false, [ 'group1' ], [], [], [] ],

			// Annotation Serialize group

			[ new ApiDescribe('EntityClass1', false, [ 'group1' ]), null, new MetadataSerialize(200, [ 'group4' ], []), null, 'EntityClass1', false, [ 'group4', 'group1' ], [], [], [] ],
			[ null, new ApiDescribe('EntityClass1', false, [ 'group1' ]), new MetadataSerialize(200, [ 'group4' ], []), null, 'EntityClass1', false, [ 'group4', 'group1' ], [], [], [] ],
			[ new ApiDescribe('EntityClass1', false, [ 'group1' ]), new ApiDescribe('EntityClass1', false, [ 'group1' ]), new MetadataSerialize(200, [ 'group4' ], []), null, 'EntityClass1', false, [ 'group4', 'group1' ], [], [], [] ],

			[ new ApiDescribe('EntityClass1', false, [ 'group1' ]), null, new MetadataSerialize(200, [ 'group4', 'group5' ], []), null, 'EntityClass1', false, [ 'group4', 'group5', 'group1' ], [], [], [] ],
			[ null, new ApiDescribe('EntityClass1', false, [ 'group1' ]), new MetadataSerialize(200, [ 'group4', 'group5' ], []), null, 'EntityClass1', false, [ 'group4', 'group5', 'group1' ], [], [], [] ],
			[ new ApiDescribe('EntityClass1', false, [ 'group1' ]), new ApiDescribe('EntityClass1', false, [ 'group1' ]), new MetadataSerialize(200, [ 'group1', 'group4', 'group5' ], []), null, 'EntityClass1', false, [ 'group1', 'group4', 'group5' ], [], [], [] ],

			// Describe Unserialize group

			[ new ApiDescribe('EntityClass1', false, [], [  'group1' ]), null, null, null, 'EntityClass1', false, [], [ 'group1' ], [], [] ],
			[ null, new ApiDescribe('EntityClass1', false, [], [  'group1' ]), null, null, 'EntityClass1', false, [], [ 'group1' ], [], [] ],
			[ new ApiDescribe('EntityClass1', false, [], [  'group1' ]), new ApiDescribe('EntityClass1', false, [], [  'group1' ]), null, null, 'EntityClass1', false, [], [ 'group1' ], [], [] ],

			[ new ApiDescribe('EntityClass1', false, [], [ 'group1' ]), null, null, null, 'EntityClass1', false, [], [ 'group1' ], [], [] ],
			[ null, new ApiDescribe('EntityClass1', false, [], [ 'group1' ]), null, null, 'EntityClass1', false, [], [ 'group1' ], [], [] ],
			[ new ApiDescribe('EntityClass1', false, [], [ 'group1' ]), new ApiDescribe('EntityClass1', false, [],[ 'group2' ]), null, null, 'EntityClass1', false, [], [ 'group1', 'group2' ], [], [] ],

			// Annotation Unserialize group

			[ new ApiDescribe('EntityClass1', false, [], [  'group1' ]), null, null, new MetadataUnserialize('', [ 'group4' ], false), 'EntityClass1', false, [], [ 'group4', 'group1' ], [], [] ],
			[ null, new ApiDescribe('EntityClass1', false, [], [  'group1' ]), null, new MetadataUnserialize('', [ 'group4' ], false), 'EntityClass1', false, [], [ 'group4', 'group1' ], [], [] ],
			[ new ApiDescribe('EntityClass1', false, [], [  'group1' ]), new ApiDescribe('EntityClass1', false, [], [  'group1' ]), null, new MetadataUnserialize('', [ 'group4' ], false), 'EntityClass1', false, [], [ 'group4', 'group1' ], [], [] ],
			
			[ new ApiDescribe('EntityClass1', false, [], [  'group1' ]), null, null, new MetadataUnserialize('', [ 'group4', 'group5' ], false), 'EntityClass1', false, [], [ 'group4', 'group5', 'group1' ], [], [] ],
			[ null, new ApiDescribe('EntityClass1', false, [], [  'group1' ]), null, new MetadataUnserialize('', [ 'group4', 'group5' ], false), 'EntityClass1', false, [], [ 'group4', 'group5', 'group1' ], [], [] ],
			[ new ApiDescribe('EntityClass1', false, [], [  'group1' ]), new ApiDescribe('EntityClass1', false, [], [  'group1' ]), null, new MetadataUnserialize('', [ 'group1', 'group4', 'group5' ], false), 'EntityClass1', false, [], [ 'group1', 'group4', 'group5' ], [], [] ],

			// Others
			[ new ApiDescribe('EntityClass1', false, [], [], [ 'KEY' => 'VALUE' ]), null, null, null, 'EntityClass1', false, [], [], [ 'KEY' => 'VALUE' ], [] ],
			[ new ApiDescribe('EntityClass1', false, [], [], [], [ 'KEY' => 'VALUE' ]), null, null, null, 'EntityClass1', false, [], [], [], [ 'KEY' => 'VALUE' ] ],
		];
	}
	
	/**
	 * @dataProvider  providerCreateMatadata
	 */
	public function testCreateMetadata(
		$describeClass, $describeMethod, $metadataSerialize, $metadataUnserialize,
		$entity,
		$isCollection,
		$serializeGroups,
		$unserializeGroups,
		$request,
		$response
	) {
		
		$router                     = $this->getMockForAbstractClass(RouterInterface::class);
		$controllerActionExtractor  = $this->getMockForAbstractClass(ControllerActionExtractorInterface::class);
		$metadataSerializeManager   = $this->getMockForAbstractClass(MetadataSerializeManagerInterface::class);
		$metadataUnserializeManager = $this->getMockForAbstractClass(MetadataUnserializeManagerInterface::class);
		$mock                       = $this->getMockBuilder(AbstractDecoratorHandler::class)->disableOriginalConstructor()->getMock();
		$controllerAction           = new ControllerAction(DummyController::class, 'dummyAction');

		$handler = new AbstractDecoratorHandlerMockAbstract(
			$router,
			$controllerActionExtractor,
			$metadataSerializeManager,
			$metadataUnserializeManager,
			$mock
		);

		$route = $this->getMockBuilder(Route::class)->disableOriginalConstructor()->getMock();
		
		$mock
			->expects($this->once())
			->method('getClassDecorator')
			->willReturnCallback(function ($rClass) use ($describeClass){
				$this->assertInstanceOf(\ReflectionClass::class, $rClass);
				$this->assertEquals($rClass->getName(), DummyController::class);
				return $describeClass;
			})
		;

		$mock
			->expects($this->once())
			->method('getMethodDecorator')
			->willReturnCallback(function ($rMethod) use ($describeMethod) {
				$this->assertInstanceOf(\ReflectionMethod::class, $rMethod);
				$this->assertEquals($rMethod->getName(), 'dummyAction');
				return $describeMethod;
			})
		;
		
		$metadataSerializeManager
			->expects($this->once())
			->method('getMetadata')
			->with(DummyController::class, 'dummyAction')
			->willReturn($metadataSerialize)
		;
		
		$metadataUnserializeManager
			->expects($this->once())
			->method('getMetadata')
			->with(DummyController::class, 'dummyAction')
			->willReturn($metadataUnserialize)
		;

		/** @var Metadata $metadata */
		$metadata = $this->reflectionCallMethod($handler, 'createMetadata', [ $route, $controllerAction ]);

		$this->assertEquals($metadata->getRoute(), $route);
		$this->assertEquals($metadata->getController(), DummyController::class);
		$this->assertEquals($metadata->getAction(), 'dummyAction');
		$this->assertEquals($metadata->getEntity(), $entity);
		$this->assertEquals($metadata->isCollection(), $isCollection);
		$this->assertEquals($metadata->getSerializeGroups(), $serializeGroups);
		$this->assertEquals($metadata->getUnSerializeGroups(), $unserializeGroups);
		$this->assertEquals($metadata->getRequest(), $request);
		$this->assertEquals($metadata->getResponse(), $response);
		$this->assertEquals($metadata->getSerialize(), $metadataSerialize);
		$this->assertEquals($metadata->getUnserialize(), $metadataUnserialize);
	}
	
	public function providerCreateMetadataNull() {
		return [
			[ null, null ],
			[ new ApiDescribe(), null ],
			[ null, new ApiDescribe() ],
			[ new ApiDescribe(), new ApiDescribe() ],
		];
	}
	
	/**
	 * @dataProvider  providerCreateMetadataNull
	 */
	public function testCreateMetadataNull($describeClass, $describeMethod) {
		
		$router                     = $this->getMockForAbstractClass(RouterInterface::class);
		$controllerActionExtractor  = $this->getMockForAbstractClass(ControllerActionExtractorInterface::class);
		$metadataSerializeManager   = $this->getMockForAbstractClass(MetadataSerializeManagerInterface::class);
		$metadataUnserializeManager = $this->getMockForAbstractClass(MetadataUnserializeManagerInterface::class);
		$mock                       = $this->getMockBuilder(AbstractDecoratorHandler::class)->disableOriginalConstructor()->getMock();
		$controllerAction           = new ControllerAction(DummyController::class, 'dummyAction');

		$annotationHandler = new AbstractDecoratorHandlerMockAbstract(
			$router,
			$controllerActionExtractor,
			$metadataSerializeManager,
			$metadataUnserializeManager,
			$mock
		);

		$route = $this->getMockBuilder(Route::class)->disableOriginalConstructor()->getMock();
		
		$mock
			->expects($this->once())
			->method('getClassDecorator')
			->willReturnCallback(function ($rClass) use ($describeClass){
				$this->assertInstanceOf(\ReflectionClass::class, $rClass);
				$this->assertEquals($rClass->getName(), DummyController::class);
				return $describeClass;
			})
		;
		
		$mock
			->expects($this->once())
			->method('getMethodDecorator')
			->willReturnCallback(function ($rMethod) use ($describeMethod) {
				$this->assertInstanceOf(\ReflectionMethod::class, $rMethod);
				$this->assertEquals($rMethod->getName(), 'dummyAction');
				return $describeMethod;
			})
		;

		
		$this->assertNull(
			$this->reflectionCallMethod($annotationHandler, 'createMetadata', [ $route, $controllerAction ])
		);
	}

}
