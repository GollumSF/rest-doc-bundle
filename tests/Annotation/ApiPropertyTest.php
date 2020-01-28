<?php

namespace Test\GollumSF\RestDocBundle\Annotation;

use GollumSF\RestDocBundle\Annotation\ApiProperty;
use PHPUnit\Framework\TestCase;

class ApiPropertyTest extends TestCase {

	public function provideConstruct() {
		return [
			[ [], null, null ],
			[ [ 'type' => 'TYPE' ], 'TYPE', null ],
			[ [ 'collection' => true ], null, true ],
			[ [ 'collection' => false ], null, false ],
		];
	}

	/**
	 * @dataProvider provideConstruct
	 */
	public function testConstruct($param, $type, $collection) {
		$annotation = new ApiProperty($param);
		$this->assertEquals($annotation->type      , $type);
		$this->assertEquals($annotation->collection, $collection);
	}
}