<?php

namespace Test\GollumSF\RestDocBundle\TypeDiscover\Handler;

use Doctrine\Common\Annotations\Reader;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\TypeDiscover\Handler\AnnotationHandler;
use PHPUnit\Framework\TestCase;

class DummyClass {
	
	private $dummyProp;
	
}


class AnnotationHandlerTest extends TestCase {
	
	public function testGetType() {

		$reader = $this->getMockBuilder(Reader::class)->disableOriginalConstructor()->getMock();
		$modelBuilder = $this->getMockBuilder(ModelBuilderInterface::class)->getMockForAbstractClass();
		
		$annotationHandler = new AnnotationHandler(
			$reader,
			$modelBuilder
		);
		
		$this->assertNull(
			$annotationHandler->getType(\stdClass::class, 'stubName')
		);
	}
//
//	public function testGetType() {
//
//		$reader = $this->getMockBuilder(Reader::class)->disableOriginalConstructor()->getMock();
//		$modelBuilder = $this->getMockBuilder(ModelBuilderInterface::class)->getMockForAbstractClass();
//
//		$annotationHandler = new AnnotationHandler(
//			$reader,
//			$modelBuilder
//		);
//
//		$this->assertNull(
//			$annotationHandler->getType(\stdClass::class, 'stubName')
//		);
//	}
//
//	/**
//	 * @return Metadata[]
//	 */
//	public function getMetadataCollection(): array {
//
//		$etadataCollection = [];
//
//		$controllerActions = [];
//		foreach ($this->router->getRouteCollection() as $routeName => $route) {
//			$controllerAction = $this->controllerActionExtractor->extractFromRoute($route);
//			if ($controllerAction) {
//				$controllerActions[] = $controllerAction;
//			}
//		}
//
//		foreach ($controllerActions as $controllerAction) {
//
//			$route = $controllerAction->getRoute();
//			$controller = $controllerAction->getControllerClass();
//			$action = $controllerAction->getAction();
//
//			$metadata = $this->createMatadata($route, $controller, $action);
//			if ($metadata) {
//				$etadataCollection[] = $metadata;
//			}
//		}
//		
//		return $etadataCollection;
//	}
//
//	protected function createMatadata(Route $route, string $controller, $action): ?Metadata {
//
//		$rClass = new \ReflectionClass($controller);
//		$rMethod = $rClass->getMethod($action);
//
//		/** @var ApiDescribe $describeClass */
//		/** @var ApiDescribe $describeMethod */
//		$describeClass = $this->reader->getClassAnnotation($rClass, ApiDescribe::class);
//		$describeMethod = $this->reader->getMethodAnnotation($rMethod, ApiDescribe::class);
//
//		$entity = null;
//		$isCollection = null;
//
//		if ($describeMethod) {
//			$entity = $describeMethod->entity;
//			$isCollection = $describeMethod->collection;
//		}
//		if ($describeClass) {
//			if ($entity === null) {
//				$entity = $describeClass->entity;
//			}
//			if ($isCollection === null) {
//				$isCollection = $describeClass->collection;
//			}
//		}
//
//		if ($entity) {
//
//			/** @var Serialize $annoSerialize */
//			/** @var Unserialize $annoUnserialize */
//			$annoSerialize = $this->reader->getMethodAnnotation($rMethod, Serialize::class);
//			$annoUnserialize = $this->reader->getMethodAnnotation($rMethod, Unserialize::class);
//
//			$serializeGroups   = $annoSerialize   && $annoSerialize->groups   ? $annoSerialize->groups   : [];
//			$unserializeGroups = $annoUnserialize && $annoUnserialize->groups ? $annoUnserialize->groups : [];
//			
//			if ($serializeGroups   && !is_array($serializeGroups  )) $serializeGroups   = [$serializeGroups];
//			if ($unserializeGroups && !is_array($unserializeGroups)) $unserializeGroups = [$unserializeGroups];
//
//			if ($describeClass  && $describeClass ->serializeGroups) $serializeGroups = array_merge($serializeGroups, $describeClass ->serializeGroups);
//			if ($describeMethod && $describeMethod->serializeGroups) $serializeGroups = array_merge($serializeGroups, $describeMethod->serializeGroups);
//
//			if ($describeClass  && $describeClass ->unserializeGroups) $unserializeGroups = array_merge($unserializeGroups, $describeClass ->unserializeGroups);
//			if ($describeMethod && $describeMethod->unserializeGroups) $unserializeGroups = array_merge($unserializeGroups, $describeMethod->unserializeGroups);
//
//			$serializeGroups   = array_unique($serializeGroups);
//			$unserializeGroups = array_unique($unserializeGroups);
//
//			$requestProperties = [];
//			if ($describeClass  && $describeClass->requestProperties)  $requestProperties = array_merge($requestProperties, $describeClass->requestProperties);
//			if ($describeMethod && $describeMethod->requestProperties) $requestProperties = array_merge($requestProperties, $describeMethod->requestProperties);
//			
//			$requestBodyProperties = [];
//			if ($describeClass  && $describeClass->requestBodyProperties)  $requestBodyProperties = array_merge($requestBodyProperties, $describeClass->requestBodyProperties);
//			if ($describeMethod && $describeMethod->requestBodyProperties) $requestBodyProperties = array_merge($requestBodyProperties, $describeMethod->requestBodyProperties);
//
//			$responseBodyProperties = [];
//			if ($describeClass  && $describeClass->responseBodyProperties)  $responseBodyProperties = array_merge($responseBodyProperties, $describeClass->responseBodyProperties);
//			if ($describeMethod && $describeMethod->responseBodyProperties) $responseBodyProperties = array_merge($responseBodyProperties, $describeMethod->responseBodyProperties);
//				
//			
//			return new Metadata(
//				$route,
//				$controller,
//				$action,
//				$entity,
//				!!$isCollection,
//				$serializeGroups,
//				$unserializeGroups,
//				$requestProperties,
//				$requestBodyProperties,
//				$responseBodyProperties,
//				$annoSerialize,
//				$annoUnserialize
//			);
//		}
//
//		return null;
//	}
}