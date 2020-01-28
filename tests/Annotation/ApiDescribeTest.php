<?php

namespace Test\GollumSF\RestDocBundle\Annotation;

use GollumSF\RestDocBundle\Annotation\ApiDescribe;
use PHPUnit\Framework\TestCase;

class ApiDescribeTest extends TestCase {

	public function provideConstruct() {
		return [
			[ [], null, null, [], [], [], [], [], [] ],
			
			[ [ 'entity' => \stdClass::class ], \stdClass::class, null, [], [], [], [], [], [] ],
			
			[ [ 'collection' => true ] , null, true, [], [], [], [], [], [] ],
			[ [ 'collection' => false ], null, false, [], [], [], [], [], [] ],

			[ [ 'collection' => false ], null, false, [], [], [], [], [], [] ],
			[ [ 'collection' => false ], null, false, [], [], [], [], [], [] ],

			[ [ 'serializeGroups' => 'group1' ], null, null, [ 'group1' ], [], [], [], [], [] ],
			[ [ 'serializeGroups' => [ 'group1', 'group2' ] ], null, null, [ 'group1', 'group2' ], [], [], [], [], [] ],

			[ [ 'unserializeGroups' => 'group1' ], null, null, [], [ 'group1' ], [], [], [], [] ],
			[ [ 'unserializeGroups' => [ 'group1', 'group2' ] ], null, null, [], [ 'group1', 'group2' ], [], [], [], [] ],

			[ [ 'requestProperties'      => [ 'key' => 'value' ] ], null, null, [], [], [ 'key' => 'value' ], [], [] ],
			[ [ 'requestBodyProperties'  => [ 'key' => 'value' ] ], null, null, [], [], [], [ 'key' => 'value' ], [] ],
			[ [ 'responseBodyProperties' => [ 'key' => 'value' ] ], null, null, [], [], [], [], [ 'key' => 'value' ] ],
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
		$requestProperties,
		$requestBodyProperties,
		$responseBodyProperties
	) {

		$annotation = new ApiDescribe($param);
		$this->assertEquals($annotation->entity                , $entity);
		$this->assertEquals($annotation->collection            , $collection);
		$this->assertEquals($annotation->serializeGroups       , $serializeGroups);
		$this->assertEquals($annotation->unserializeGroups     , $unserializeGroups);
		$this->assertEquals($annotation->requestProperties     , $requestProperties);
		$this->assertEquals($annotation->requestBodyProperties , $requestBodyProperties);
		$this->assertEquals($annotation->responseBodyProperties, $responseBodyProperties);
	}
}