<?php

namespace Test\GollumSF\RestDocBundle\Builder\MetadataBuilder\Handler;

use Doctrine\Common\Annotations\Reader;
use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestBundle\Annotation\Serialize;
use GollumSF\RestBundle\Annotation\Unserialize;
use GollumSF\RestBundle\Reflection\ControllerAction;
use GollumSF\RestBundle\Reflection\ControllerActionExtractorInterface;
use GollumSF\RestDocBundle\Annotation\ApiDescribe;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\Handler\AnnotationHandler;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class AnnotationHandlerGetMetadataCollection extends AnnotationHandler {

	private $metadatas;

	public $routes = [];
	public $controllers = [];
	public $actions = [];
	
	public function __construct(
		RouterInterface $router,
		Reader $reader,
		ControllerActionExtractorInterface $controllerActionExtractor,
		array $metadatas
	) {
		parent::__construct($router, $reader, $controllerActionExtractor);
		$this->metadatas = $metadatas;
	}

	protected function createMatadata(Route $route, string $controller, $action): ?Metadata {
		$this->routes     [] = $route;
		$this->controllers[] = $controller;
		$this->actions    [] = $action;
		return array_pop($this->metadatas);
	}
	
}

class DummyController {
	public function dummyAction() {
	}
}

class AnnotationHandlerTest extends TestCase {
	
	use ReflectionPropertyTrait;
	
	public function testGetMetadataCollection() {

		$router                    = $this->getMockBuilder(RouterInterface::class                   )->getMockForAbstractClass();
		$reader                    = $this->getMockBuilder(Reader::class                            )->disableOriginalConstructor()->getMock();
		$controllerActionExtractor = $this->getMockBuilder(ControllerActionExtractorInterface::class)->getMockForAbstractClass();

		$metadata1 = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata2 = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata3 = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();

		$route1 = $this->getMockBuilder(Route::class)->disableOriginalConstructor()->getMock();
		$route2 = $this->getMockBuilder(Route::class)->disableOriginalConstructor()->getMock();
		$route3 = $this->getMockBuilder(Route::class)->disableOriginalConstructor()->getMock();

		$route1->expects($this->once())->method('getDefault')->with('_controller')->willReturn('controller1::action1');
		$route2->expects($this->once())->method('getDefault')->with('_controller')->willReturn('controller2::action2');
		$route3->expects($this->once())->method('getDefault')->with('_controller')->willReturn('controller3::action3');
		
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
			->expects($this->at(0))
			->method('extractFromString')
			->with('controller1::action1')
			->willReturn($controllerAction1)
		;
		$controllerActionExtractor
			->expects($this->at(1))
			->method('extractFromString')
			->with('controller2::action2')
			->willReturn($controllerAction2)
		;
		$controllerActionExtractor
			->expects($this->at(2))
			->method('extractFromString')
			->with('controller3::action3')
			->willReturn($controllerAction3)
		;
		
		$annotationHandler = new AnnotationHandlerGetMetadataCollection(
			$router,
			$reader,
			$controllerActionExtractor,
			[
				$metadata1,
				$metadata2,
				$metadata3
			]
		);
		
		$collection = $annotationHandler->getMetadataCollection();
		
		$this->assertEquals($collection, [
			$metadata1,
			$metadata2,
			$metadata3
		]);

		$this->assertEquals($annotationHandler->routes, [
			$route1,
			$route2,
			$route3
		]);

		$this->assertEquals($annotationHandler->controllers, [
			'controller1',
			'controller2',
			'controller3'
		]);

		$this->assertEquals($annotationHandler->actions, [
			'action1',
			'action2',
			'action3'
		]);
	}
	
	public function providerCreateMatadata() {
		return [
			
			// Entity Class
			
			[ new ApiDescribe([ 'entity' => 'EntityClass1' ]), null, null, null, 'EntityClass1', false, [], [], [], [], [] ],
			[ null, new ApiDescribe([ 'entity' => 'EntityClass1' ]), null, null, 'EntityClass1', false, [], [], [], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1' ]), new ApiDescribe([ 'entity' => 'EntityClass2' ]), null, null, 'EntityClass2', false, [], [], [], [], [] ],

			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'collection' => true ]), null, null, null, 'EntityClass1', true, [], [], [], [], [] ],
			[ null, new ApiDescribe([ 'entity' => 'EntityClass1', 'collection' => true ]), null, null, 'EntityClass1', true, [], [], [], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'collection' => true ]), new ApiDescribe([ 'entity' => 'EntityClass2', 'collection' => false ]), null, null, 'EntityClass2', false, [], [], [], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'collection' => false ]), new ApiDescribe([ 'entity' => 'EntityClass2', 'collection' => true ]), null, null, 'EntityClass2', true, [], [], [], [], [] ],
			
			// Describe Serialize group

			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), null, null, null, 'EntityClass1', false, [ 'group1' ], [], [], [], [] ],
			[ null, new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), null, null, 'EntityClass1', false, [ 'group1' ], [], [], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), null, null, 'EntityClass1', false, [ 'group1' ], [], [], [], [] ],

			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => [ 'group1' ] ]), null, null, null, 'EntityClass1', false, [ 'group1' ], [], [], [], [] ],
			[ null, new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => [ 'group1' ] ]), null, null, 'EntityClass1', false, [ 'group1' ], [], [], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => [ 'group1' ] ]), new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => [ 'group2' ] ]), null, null, 'EntityClass1', false, [ 'group1', 'group2' ], [], [], [], [] ],

			// Annotation Serialize group

			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), null, new Serialize([ 'groups' => 'group4' ]), null, 'EntityClass1', false, [ 'group4', 'group1' ], [], [], [], [] ],
			[ null, new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), new Serialize([ 'groups' => 'group4' ]), null, 'EntityClass1', false, [ 'group4', 'group1' ], [], [], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), new Serialize([ 'groups' => 'group4' ]), null, 'EntityClass1', false, [ 'group4', 'group1' ], [], [], [], [] ],

			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), null, new Serialize([ 'groups' => [ 'group4', 'group5' ] ]), null, 'EntityClass1', false, [ 'group4', 'group5', 'group1' ], [], [], [], [] ],
			[ null, new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), new Serialize([ 'groups' => [ 'group4', 'group5' ] ]), null, 'EntityClass1', false, [ 'group4', 'group5', 'group1' ], [], [], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), new Serialize([ 'groups' => [ 'group1', 'group4', 'group5' ] ]), null, 'EntityClass1', false, [ 'group1', 'group4', 'group5' ], [], [], [], [] ],

			// Describe Unserialize group

			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), null, null, null, 'EntityClass1', false, [], [ 'group1' ], [], [], [] ],
			[ null, new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), null, null, 'EntityClass1', false, [], [ 'group1' ], [], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), null, null, 'EntityClass1', false, [], [ 'group1' ], [], [], [] ],

			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => [ 'group1' ] ]), null, null, null, 'EntityClass1', false, [], [ 'group1' ], [], [], [] ],
			[ null, new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => [ 'group1' ] ]), null, null, 'EntityClass1', false, [], [ 'group1' ], [], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => [ 'group1' ] ]), new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => [ 'group2' ] ]), null, null, 'EntityClass1', false, [], [ 'group1', 'group2' ], [], [], [] ],

			// Annotation Unserialize group

			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), null, null, new Unserialize([ 'groups' => 'group4' ]), 'EntityClass1', false, [], [ 'group4', 'group1' ], [], [], [] ],
			[ null, new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), null, new Unserialize([ 'groups' => 'group4' ]), 'EntityClass1', false, [], [ 'group4', 'group1' ], [], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), null, new Unserialize([ 'groups' => 'group4' ]), 'EntityClass1', false, [], [ 'group4', 'group1' ], [], [], [] ],
			
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), null, null, new Unserialize([ 'groups' => [ 'group4', 'group5' ] ]), 'EntityClass1', false, [], [ 'group4', 'group5', 'group1' ], [], [], [] ],
			[ null, new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), null, new Unserialize([ 'groups' => [ 'group4', 'group5' ] ]), 'EntityClass1', false, [], [ 'group4', 'group5', 'group1' ], [], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), null, new Unserialize([ 'groups' => [ 'group1', 'group4', 'group5' ] ]), 'EntityClass1', false, [], [ 'group1', 'group4', 'group5' ], [], [], [] ],

			// Others
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'requestProperties' => [ 'KEY' => 'VALUE' ] ]), null, null, null, 'EntityClass1', false, [], [], [ 'KEY' => 'VALUE' ], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'requestBodyProperties' => [ 'KEY' => 'VALUE' ] ]), null, null, null, 'EntityClass1', false, [], [], [], [ 'KEY' => 'VALUE' ], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'responseBodyProperties' => [ 'KEY' => 'VALUE' ] ]), null, null, null, 'EntityClass1', false, [], [], [], [], [ 'KEY' => 'VALUE' ] ],
		];
	}
	
	/**
	 * @dataProvider  providerCreateMatadata
	 */
	public function testCreateMatadata(
		$describeClass, $describeMethod, $annoSerialize, $annoUnserialize,
		$entity,
		$isCollection,
		$serializeGroups,
		$unserializeGroups,
		$requestProperties,
		$requestBodyProperties,
		$responseBodyProperties
	) {

		$router                    = $this->getMockBuilder(RouterInterface::class                   )->getMockForAbstractClass();
		$reader                    = $this->getMockBuilder(Reader::class                            )->disableOriginalConstructor()->getMock();
		$controllerActionExtractor = $this->getMockBuilder(ControllerActionExtractorInterface::class)->getMockForAbstractClass();

		$annotationHandler = new AnnotationHandler(
			$router,
			$reader,
			$controllerActionExtractor
		);

		$route = $this->getMockBuilder(Route::class)->disableOriginalConstructor()->getMock();

		$reader
			->expects($this->at(0))
			->method('getClassAnnotation')
			->willReturnCallback(function ($rClass, $annoClass) use ($describeClass){
				$this->assertInstanceOf(\ReflectionClass::class, $rClass);
				$this->assertEquals($rClass->getName(), DummyController::class);
				$this->assertEquals($annoClass, ApiDescribe::class);
				return $describeClass;
			})
		;

		$reader
			->expects($this->at(1))
			->method('getMethodAnnotation')
			->willReturnCallback(function ($rMethod, $annoClass) use ($describeMethod) {
				$this->assertInstanceOf(\ReflectionMethod::class, $rMethod);
				$this->assertEquals($rMethod->getName(), 'dummyAction');
				$this->assertEquals($annoClass, ApiDescribe::class);
				return $describeMethod;
			})
		;
		$reader
			->expects($this->at(2))
			->method('getMethodAnnotation')
			->willReturnCallback(function ($rMethod, $annoClass) use ($annoSerialize) {
				$this->assertInstanceOf(\ReflectionMethod::class, $rMethod);
				$this->assertEquals($rMethod->getName(), 'dummyAction');
				$this->assertEquals($annoClass, Serialize::class);
				return $annoSerialize;
			})
		;
		$reader
			->expects($this->at(3))
			->method('getMethodAnnotation')
			->willReturnCallback(function ($rMethod, $annoClass) use ($annoUnserialize) {
				$this->assertInstanceOf(\ReflectionMethod::class, $rMethod);
				$this->assertEquals($rMethod->getName(), 'dummyAction');
				$this->assertEquals($annoClass, Unserialize::class);
				return $annoUnserialize;
			})
		;

		/** @var Metadata $metadata */
		$metadata = $this->reflectionCallMethod($annotationHandler, 'createMatadata', [ $route, DummyController::class, 'dummyAction' ]);

		$this->assertEquals($metadata->getRoute(), $route);
		$this->assertEquals($metadata->getController(), DummyController::class);
		$this->assertEquals($metadata->getAction(), 'dummyAction');
		$this->assertEquals($metadata->getEntity(), $entity);
		$this->assertEquals($metadata->isCollection(), $isCollection);
		$this->assertEquals($metadata->getSerializeGroups(), $serializeGroups);
		$this->assertEquals($metadata->getUnSerializeGroups(), $unserializeGroups);
		$this->assertEquals($metadata->getRequestProperties(), $requestProperties);
		$this->assertEquals($metadata->getRequestBodyProperties(), $requestBodyProperties);
		$this->assertEquals($metadata->getResponseBodyProperties(), $responseBodyProperties);
		$this->assertEquals($metadata->getSerialize(), $annoSerialize);
		$this->assertEquals($metadata->getUnserialize(), $annoUnserialize);
	}
	
	public function providerCreateMatadataNull() {
		return [
			[ null, null ],
			[ new ApiDescribe([]), null ],
			[ null, new ApiDescribe([]) ],
			[ new ApiDescribe([]), new ApiDescribe([]) ],
		];
	}
	
	/**
	 * @dataProvider  providerCreateMatadataNull
	 */
	public function testCreateMatadataNull($describeClass, $describeMethod) {

		$router                    = $this->getMockBuilder(RouterInterface::class                   )->getMockForAbstractClass();
		$reader                    = $this->getMockBuilder(Reader::class                            )->disableOriginalConstructor()->getMock();
		$controllerActionExtractor = $this->getMockBuilder(ControllerActionExtractorInterface::class)->getMockForAbstractClass();

		$annotationHandler = new AnnotationHandler(
			$router,
			$reader,
			$controllerActionExtractor
		);

		$route = $this->getMockBuilder(Route::class)->disableOriginalConstructor()->getMock();

		$reader
			->expects($this->at(0))
			->method('getClassAnnotation')
			->willReturnCallback(function ($rClass, $annoClass) use ($describeClass){
				$this->assertInstanceOf(\ReflectionClass::class, $rClass);
				$this->assertEquals($rClass->getName(), DummyController::class);
				$this->assertEquals($annoClass, ApiDescribe::class);
				return $describeClass;
			})
		;

		$reader
			->expects($this->at(1))
			->method('getMethodAnnotation')
			->willReturnCallback(function ($rMethod, $annoClass) use ($describeMethod) {
				$this->assertInstanceOf(\ReflectionMethod::class, $rMethod);
				$this->assertEquals($rMethod->getName(), 'dummyAction');
				$this->assertEquals($annoClass, ApiDescribe::class);
				return $describeMethod;
			})
		;

		
		$this->assertNull(
			$this->reflectionCallMethod($annotationHandler, 'createMatadata', [ $route, DummyController::class, 'dummyAction' ])
		);
	}

}