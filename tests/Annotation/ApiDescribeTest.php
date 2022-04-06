<?php

namespace Test\GollumSF\RestDocBundle\Annotation;

use GollumSF\RestDocBundle\Annotation\ApiDescribe;
use PHPUnit\Framework\TestCase;

class ApiDescribeTest extends TestCase {
	
	public function provideConstructLagacy() {
		return [
			[ [], null, null, [], [], [], [], null ],
			
			[ [ 'value' => \stdClass::class ], \stdClass::class, null, [], [], [], [], null ],
			[ [ 'entity' => \stdClass::class ], \stdClass::class, null, [], [], [], [], null ],
			
			[ [ 'collection' => true ] , null, true, [], [], [], [], null ],
			[ [ 'collection' => false ], null, false, [], [], [], [], null ],
			
			[ [ 'collection' => false ], null, false, [], [], [], [], null ],
			[ [ 'collection' => false ], null, false, [], [], [], [], null ],
			
			[ [ 'serializeGroups' => 'group1' ], null, null, [ 'group1' ], [], [], [], null ],
			[ [ 'serializeGroups' => [ 'group1', 'group2' ] ], null, null, [ 'group1', 'group2' ], [], [], [], null ],
			
			[ [ 'unserializeGroups' => 'group1' ], null, null, [], [ 'group1' ], [], [], null ],
			[ [ 'unserializeGroups' => [ 'group1', 'group2' ] ], null, null, [], [ 'group1', 'group2' ], [], [], null ],
			
			[ [ 'summary'  => 'SUMMARY' ], null, null, [], [], [], [], 'SUMMARY' ],
			
			[ [ 'request'  => [ 'key' => 'value' ] ], null, null, [], [], [ 'key' => 'value' ], [], null ],
			[ [ 'response' => [ 'key' => 'value' ] ], null, null, [], [], [], [ 'key' => 'value' ], null ],
		];
	}
	
	/**
	 * @dataProvider provideConstructLagacy
	 */
	public function testConstructLagacy(
		$param,
		$entity,
		$collection,
		$serializeGroups,
		$unserializeGroups,
		$request,
		$response,
		$summary
	) {
		$annotation = new ApiDescribe($param);
		$this->assertEquals($annotation->getEntity()           , $entity);
		$this->assertEquals($annotation->isCollection()        , $collection);
		$this->assertEquals($annotation->getSerializeGroups()  , $serializeGroups);
		$this->assertEquals($annotation->getUnserializeGroups(), $unserializeGroups);
		$this->assertEquals($annotation->getRequest()          , $request);
		$this->assertEquals($annotation->getResponse()         , $response);
		$this->assertEquals($annotation->getSummary()          , $summary);
	}
	
	
	public function provideConstruct() {
		return [
			[ null, false, [], [], [], [], null, [], [] ],
			
			[ \stdClass::class, false, [], [], [], [], null, [], [] ],
			[ \stdClass::class, false, [], [], [], [], null, [], [] ],
			
			[ null, true, [], [], [], [], null, [], [] ],
			[ null, false, [], [], [], [], null, [], [] ],
			
			[ null, false, [], [], [], [], null, [], [] ],
			[ null, false, [], [], [], [], null, [], [] ],
			
			[ null, false, 'group1', [], [], [], null, [ 'group1' ], [] ],
			[ null, false, [ 'group1', 'group2' ], [], [], [], null, [ 'group1', 'group2' ], [] ],
			
			[ null, false, [], 'group1', [], [], null, [], [ 'group1' ] ],
			[ null, false, [], [ 'group1', 'group2' ], [], [], null, [], [ 'group1', 'group2' ] ],
			
			[ null, false, [], [], [], [], 'SUMMARY', [], [] ],
			
			[ null, false, [], [], [ 'key' => 'value' ], [], null, [], [] ],
			[ null, false, [], [], [], [ 'key' => 'value' ], null, [], [] ],
		];
	}
	
	/**
	 * @dataProvider provideConstruct
	 */
	public function testConstruct(
		$entity,
		$collection,
		$serializeGroups,
		$unserializeGroups,
		$request,
		$response,
		$summary,
		$serializeGroupsResult,
		$unserializeGroupsResult
	) {
		$annotation = new ApiDescribe(
			$entity,
			$collection,
			$serializeGroups,
			$unserializeGroups,
			$request,
			$response,
			$summary
		);
		
		$this->assertEquals($annotation->getEntity()           , $entity);
		$this->assertEquals($annotation->isCollection()        , $collection);
		$this->assertEquals($annotation->getSerializeGroups()  , $serializeGroupsResult);
		$this->assertEquals($annotation->getUnserializeGroups(), $unserializeGroupsResult);
		$this->assertEquals($annotation->getRequest()          , $request);
		$this->assertEquals($annotation->getResponse()         , $response);
		$this->assertEquals($annotation->getSummary()          , $summary);
	}
}
