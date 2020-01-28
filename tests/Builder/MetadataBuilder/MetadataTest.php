<?php

namespace Test\GollumSF\RestDocBundle\Builder\MetadataBuilder;

use GollumSF\RestBundle\Annotation\Serialize;
use GollumSF\RestBundle\Annotation\Unserialize;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Route;

class MetadataTest extends TestCase {

	public function provideConstruct() {
		$route = $this->getMockBuilder(Route::class)->disableOriginalConstructor()->getMock();
		
		return [
			[
				$route,
				'CONTROLLER',
				'ACTION',
				\stdClass::class,
				true,
				[ 's_group1' ],
				[ 'us_group1' ],
				[ 'r_prop1' ],
				[ 'rb_prop1' ],
				[ 'resp_prop1' ],
				new Serialize([]),
				new Unserialize([]),
			],
			[
				$route,
				'CONTROLLER',
				'ACTION',
				\stdClass::class,
				false,
				[ 's_group1' ],
				[ 'us_group1' ],
				[ 'r_prop1' ],
				[ 'rb_prop1' ],
				[ 'resp_prop1' ],
				null,
				null,
			]
		];
	}

	/**
	 * @dataProvider provideConstruct
	 */
	public function testConstruct(
		Route $route,
		string $controller,
		string $action,
		string $entity,
		bool $collection,
		array $serializeGroups,
		array $unserializeGroups,
		array $requestProperties,
		array $requestBodyProperties,
		array $responseBodyProperties,
		?Serialize $serialize,
		?Unserialize $unserialize
	) {

		$annotation = new Metadata(
			$route,
			$controller,
			$action,
			$entity,
			$collection,
			$serializeGroups,
			$unserializeGroups,
			$requestProperties,
			$requestBodyProperties,
			$responseBodyProperties,
			$serialize,
			$unserialize
		);
		$this->assertEquals($annotation->getRoute(), $route);
		$this->assertEquals($annotation->getController(), $controller);
		$this->assertEquals($annotation->getAction(), $action);
		$this->assertEquals($annotation->getEntity(), $entity);
		$this->assertEquals($annotation->isCollection(), $collection);
		$this->assertEquals($annotation->getSerializeGroups(), $serializeGroups);
		$this->assertEquals($annotation->getUnserializeGroups(), $unserializeGroups);
		$this->assertEquals($annotation->getRequestProperties(), $requestProperties);
		$this->assertEquals($annotation->getRequestBodyProperties(), $requestBodyProperties);
		$this->assertEquals($annotation->getResponseBodyProperties(), $responseBodyProperties);
		$this->assertEquals($annotation->getSerialize(), $serialize);
		$this->assertEquals($annotation->getUnserialize(), $unserialize);
	}
	
}