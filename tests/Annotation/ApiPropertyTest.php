<?php

namespace Test\GollumSF\RestDocBundle\Annotation;

use GollumSF\RestDocBundle\Annotation\ApiProperty;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class ApiPropertyTest extends TestCase {
	
	public static function provideConstructLegacy() {
		return [
			[ [], null, null ],
			[ [ 'type' => 'TYPE' ], 'TYPE', null ],
			[ [ 'collection' => true ], null, true ],
			[ [ 'collection' => false ], null, false ],
		];
	}
	
	#[DataProvider('provideConstructLegacy')]
	public function testConstructLegacy($param, $type, $collection) {
		$annotation = new ApiProperty($param);
		$this->assertEquals($annotation->getType()     , $type);
		$this->assertEquals($annotation->isCollection(), $collection);
	}
	
	public static function provideConstruct() {
		return [
			[ null, false ],
			[ 'TYPE', false ],
			[ null, true ],
			[ null, false ],
		];
	}
	
	#[DataProvider('provideConstruct')]
	public function testConstruc($type, $collection) {
		$annotation = new ApiProperty($type, $collection);
		$this->assertEquals($annotation->getType()     , $type);
		$this->assertEquals($annotation->isCollection(), $collection);
	}
	
}
