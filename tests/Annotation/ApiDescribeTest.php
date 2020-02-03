<?php

namespace Test\GollumSF\RestDocBundle\Annotation;

use GollumSF\RestDocBundle\Annotation\ApiDescribe;
use PHPUnit\Framework\TestCase;

class ApiDescribeTest extends TestCase {

	public function provideConstruct() {
		return [
			[ [], null, null, [], [], [], [], [], [] ],

			[ [ 'value' => \stdClass::class ], \stdClass::class, null, [], [], [], [], [] ],
			[ [ 'entity' => \stdClass::class ], \stdClass::class, null, [], [], [], [], [] ],
			
			[ [ 'collection' => true ] , null, true, [], [], [], [], [] ],
			[ [ 'collection' => false ], null, false, [], [], [], [], [] ],

			[ [ 'collection' => false ], null, false, [], [], [], [], [] ],
			[ [ 'collection' => false ], null, false, [], [], [], [], [] ],

			[ [ 'serializeGroups' => 'group1' ], null, null, [ 'group1' ], [], [], [], [] ],
			[ [ 'serializeGroups' => [ 'group1', 'group2' ] ], null, null, [ 'group1', 'group2' ], [], [], [], [] ],

			[ [ 'unserializeGroups' => 'group1' ], null, null, [], [ 'group1' ], [], [], [] ],
			[ [ 'unserializeGroups' => [ 'group1', 'group2' ] ], null, null, [], [ 'group1', 'group2' ], [], [], [] ],

			[ [ 'request'  => [ 'key' => 'value' ] ], null, null, [], [], [ 'key' => 'value' ], [] ],
			[ [ 'response' => [ 'key' => 'value' ] ], null, null, [], [], [], [ 'key' => 'value' ] ],
		];
	}

	/**
	 * @dataProvider provideConstruct
	 */
	public function testConstruct(
		$param,
		$entity,
		$collection,
		$serializeGroups,
		$unserializeGroups,
		$request,
		$response
	) {
		$annotation = new ApiDescribe($param);
		$this->assertEquals($annotation->getEntity()           , $entity);
		$this->assertEquals($annotation->isCollection()        , $collection);
		$this->assertEquals($annotation->getSerializeGroups()  , $serializeGroups);
		$this->assertEquals($annotation->getUnserializeGroups(), $unserializeGroups);
		$this->assertEquals($annotation->getRequest()          , $request);
		$this->assertEquals($annotation->getResponse()         , $response);
		$this->assertEquals($annotation->getAliasName()        , ApiDescribe::ALIAS_NAME);
		$this->assertTrue($annotation->allowArray());
	}
}