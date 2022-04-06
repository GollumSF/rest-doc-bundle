<?php

namespace Test\GollumSF\RestDocBundle\Builder\MetadataBuilder;

use GollumSF\RestBundle\Metadata\Serialize\MetadataSerialize;
use GollumSF\RestBundle\Metadata\Unserialize\MetadataUnserialize;
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
				[ 'req_prop1' ],
				[ 'resp_prop1' ],
				'SUMMARY',
				new MetadataSerialize(200, [], []),
				new MetadataUnserialize('', [], false),
			],
			[
				$route,
				'CONTROLLER',
				'ACTION',
				\stdClass::class,
				false,
				[ 's_group1' ],
				[ 'us_group1' ],
				[ 'req_prop1' ],
				[ 'resp_prop1' ],
				null,
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
		array $request,
		array $response,
		?string $summary,
		?MetadataSerialize $serialize,
		?MetadataUnserialize $unserialize
	) {

		$annotation = new Metadata(
			$route,
			$controller,
			$action,
			$entity,
			$collection,
			$serializeGroups,
			$unserializeGroups,
			$request,
			$response,
			$summary,
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
		$this->assertEquals($annotation->getRequest(), $request);
		$this->assertEquals($annotation->getResponse(), $response);
		$this->assertEquals($annotation->getSummary(),  $summary);
		$this->assertEquals($annotation->getSerialize(), $serialize);
		$this->assertEquals($annotation->getUnserialize(), $unserialize);
	}
	
}
