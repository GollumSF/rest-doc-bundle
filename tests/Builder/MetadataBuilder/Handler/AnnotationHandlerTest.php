<?php

namespace Test\GollumSF\RestDocBundle\Builder\MetadataBuilder\Handler;

use Doctrine\Common\Annotations\Reader;
use GollumSF\ControllerActionExtractorBundle\Extractor\ControllerAction;
use GollumSF\ControllerActionExtractorBundle\Extractor\ControllerActionExtractorInterface;
use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestBundle\Annotation\Serialize;
use GollumSF\RestBundle\Annotation\Unserialize;
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
	public $controllerAction = [];
	
	public function __construct(
		RouterInterface $router,
		Reader $reader,
		ControllerActionExtractorInterface $controllerActionExtractor,
		array $metadatas
	) {
		parent::__construct($router, $reader, $controllerActionExtractor);
		$this->metadatas = $metadatas;
	}

	protected function createMetadata(Route $route, ControllerAction $controllerAction): ?Metadata {
		$this->routes[] = $route;
		$this->controllerAction[] = $controllerAction;
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
			->method('extractFromRoute')
			->with($route1)
			->willReturn($controllerAction1)
		;
		$controllerActionExtractor
			->expects($this->at(1))
			->method('extractFromRoute')
			->with($route2)
			->willReturn($controllerAction2)
		;
		$controllerActionExtractor
			->expects($this->at(2))
			->method('extractFromRoute')
			->with($route3)
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

		$this->assertEquals($annotationHandler->controllerAction, [
			new ControllerAction('controller1', 'action1'),
			new ControllerAction('controller2', 'action2'),
			new ControllerAction('controller3', 'action3'),
		]);
	}
	
	public function providerCreateMatadata() {
		return [
			
			// Entity Class
			
			[ new ApiDescribe([ 'entity' => 'EntityClass1' ]), null, null, null, 'EntityClass1', false, [], [], [], [] ],
			[ null, new ApiDescribe([ 'entity' => 'EntityClass1' ]), null, null, 'EntityClass1', false, [], [], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1' ]), new ApiDescribe([ 'entity' => 'EntityClass2' ]), null, null, 'EntityClass2', false, [], [], [], [] ],

			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'collection' => true ]), null, null, null, 'EntityClass1', true, [], [], [], [] ],
			[ null, new ApiDescribe([ 'entity' => 'EntityClass1', 'collection' => true ]), null, null, 'EntityClass1', true, [], [], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'collection' => true ]), new ApiDescribe([ 'entity' => 'EntityClass2', 'collection' => false ]), null, null, 'EntityClass2', false, [], [], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'collection' => false ]), new ApiDescribe([ 'entity' => 'EntityClass2', 'collection' => true ]), null, null, 'EntityClass2', true, [], [], [], [] ],
			
			// Describe Serialize group

			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), null, null, null, 'EntityClass1', false, [ 'group1' ], [], [], [] ],
			[ null, new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), null, null, 'EntityClass1', false, [ 'group1' ], [], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), null, null, 'EntityClass1', false, [ 'group1' ], [], [], [] ],

			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => [ 'group1' ] ]), null, null, null, 'EntityClass1', false, [ 'group1' ], [], [], [] ],
			[ null, new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => [ 'group1' ] ]), null, null, 'EntityClass1', false, [ 'group1' ], [], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => [ 'group1' ] ]), new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => [ 'group2' ] ]), null, null, 'EntityClass1', false, [ 'group1', 'group2' ], [], [], [] ],

			// Annotation Serialize group

			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), null, new Serialize([ 'groups' => 'group4' ]), null, 'EntityClass1', false, [ 'group4', 'group1' ], [], [], [] ],
			[ null, new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), new Serialize([ 'groups' => 'group4' ]), null, 'EntityClass1', false, [ 'group4', 'group1' ], [], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), new Serialize([ 'groups' => 'group4' ]), null, 'EntityClass1', false, [ 'group4', 'group1' ], [], [], [] ],

			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), null, new Serialize([ 'groups' => [ 'group4', 'group5' ] ]), null, 'EntityClass1', false, [ 'group4', 'group5', 'group1' ], [], [], [] ],
			[ null, new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), new Serialize([ 'groups' => [ 'group4', 'group5' ] ]), null, 'EntityClass1', false, [ 'group4', 'group5', 'group1' ], [], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), new ApiDescribe([ 'entity' => 'EntityClass1', 'serializeGroups' => 'group1' ]), new Serialize([ 'groups' => [ 'group1', 'group4', 'group5' ] ]), null, 'EntityClass1', false, [ 'group1', 'group4', 'group5' ], [], [], [] ],

			// Describe Unserialize group

			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), null, null, null, 'EntityClass1', false, [], [ 'group1' ], [], [] ],
			[ null, new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), null, null, 'EntityClass1', false, [], [ 'group1' ], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), null, null, 'EntityClass1', false, [], [ 'group1' ], [], [] ],

			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => [ 'group1' ] ]), null, null, null, 'EntityClass1', false, [], [ 'group1' ], [], [] ],
			[ null, new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => [ 'group1' ] ]), null, null, 'EntityClass1', false, [], [ 'group1' ], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => [ 'group1' ] ]), new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => [ 'group2' ] ]), null, null, 'EntityClass1', false, [], [ 'group1', 'group2' ], [], [] ],

			// Annotation Unserialize group

			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), null, null, new Unserialize([ 'groups' => 'group4' ]), 'EntityClass1', false, [], [ 'group4', 'group1' ], [], [] ],
			[ null, new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), null, new Unserialize([ 'groups' => 'group4' ]), 'EntityClass1', false, [], [ 'group4', 'group1' ], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), null, new Unserialize([ 'groups' => 'group4' ]), 'EntityClass1', false, [], [ 'group4', 'group1' ], [], [] ],
			
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), null, null, new Unserialize([ 'groups' => [ 'group4', 'group5' ] ]), 'EntityClass1', false, [], [ 'group4', 'group5', 'group1' ], [], [] ],
			[ null, new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), null, new Unserialize([ 'groups' => [ 'group4', 'group5' ] ]), 'EntityClass1', false, [], [ 'group4', 'group5', 'group1' ], [], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), new ApiDescribe([ 'entity' => 'EntityClass1', 'unserializeGroups' => 'group1' ]), null, new Unserialize([ 'groups' => [ 'group1', 'group4', 'group5' ] ]), 'EntityClass1', false, [], [ 'group1', 'group4', 'group5' ], [], [] ],

			// Others
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'request' => [ 'KEY' => 'VALUE' ] ]), null, null, null, 'EntityClass1', false, [], [], [ 'KEY' => 'VALUE' ], [] ],
			[ new ApiDescribe([ 'entity' => 'EntityClass1', 'response' => [ 'KEY' => 'VALUE' ] ]), null, null, null, 'EntityClass1', false, [], [], [], [ 'KEY' => 'VALUE' ] ],
		];
	}
	
	/**
	 * @dataProvider  providerCreateMatadata
	 */
	public function testCreateMetadata(
		$describeClass, $describeMethod, $annoSerialize, $annoUnserialize,
		$entity,
		$isCollection,
		$serializeGroups,
		$unserializeGroups,
		$request,
		$response
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
		$metadata = $this->reflectionCallMethod($annotationHandler, 'createMetadata', [ $route, new ControllerAction(DummyController::class, 'dummyAction') ]);

		$this->assertEquals($metadata->getRoute(), $route);
		$this->assertEquals($metadata->getController(), DummyController::class);
		$this->assertEquals($metadata->getAction(), 'dummyAction');
		$this->assertEquals($metadata->getEntity(), $entity);
		$this->assertEquals($metadata->isCollection(), $isCollection);
		$this->assertEquals($metadata->getSerializeGroups(), $serializeGroups);
		$this->assertEquals($metadata->getUnSerializeGroups(), $unserializeGroups);
		$this->assertEquals($metadata->getRequest(), $request);
		$this->assertEquals($metadata->getResponse(), $response);
		$this->assertEquals($metadata->getSerialize(), $annoSerialize);
		$this->assertEquals($metadata->getUnserialize(), $annoUnserialize);
	}
	
	public function providerCreateMetadataNull() {
		return [
			[ null, null ],
			[ new ApiDescribe([]), null ],
			[ null, new ApiDescribe([]) ],
			[ new ApiDescribe([]), new ApiDescribe([]) ],
		];
	}
	
	/**
	 * @dataProvider  providerCreateMetadataNull
	 */
	public function testCreateMetadataNull($describeClass, $describeMethod) {

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
			$this->reflectionCallMethod($annotationHandler, 'createMetadata', [ $route, new ControllerAction(DummyController::class, 'dummyAction') ])
		);
	}

}