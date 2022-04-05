<?php

namespace Test\GollumSF\RestDocBundle\Annotation;

use GollumSF\RestDocBundle\Annotation\ApiProperty;
use PHPUnit\Framework\TestCase;

class ApiPropertyTest extends TestCase {
	
	public function provideConstructLegacy() {
		return [
			[ [], null, null ],
			[ [ 'type' => 'TYPE' ], 'TYPE', null ],
			[ [ 'collection' => true ], null, true ],
			[ [ 'collection' => false ], null, false ],
		];
	}
	
	/**
	 * @dataProvider provideConstructLegacy
	 */
	public function testConstructLegacy($param, $type, $collection) {
		$annotation = new ApiProperty($param);
		$this->assertEquals($annotation->getType()     , $type);
		$this->assertEquals($annotation->isCollection(), $collection);
	}
	
	public function provideConstruct() {
		return [
			[ null, false ],
			[ 'TYPE', false ],
			[ null, true ],
			[ null, false ],
		];
	}
	
	/**
	 * @dataProvider provideConstruct
	 */
	public function testConstruc($type, $collection) {
		$annotation = new ApiProperty($type, $collection);
		$this->assertEquals($annotation->getType()     , $type);
		$this->assertEquals($annotation->isCollection(), $collection);
	}
	
}
