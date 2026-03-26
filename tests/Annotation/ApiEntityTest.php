<?php

namespace Test\GollumSF\RestDocBundle\Annotation;

use GollumSF\RestDocBundle\Annotation\ApiEntity;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class ApiEntityTest extends TestCase {
	
	public static function provideConstructLegacy() {
		return [
			[ [], null, null, null ],
			[ [ 'description' => 'DESCRIPTION' ], 'DESCRIPTION', null, null ],
			[ [ 'url' => 'http://URL' ], null, 'http://URL', null ],
			[ [ 'docDescription' => 'DOC DESCRIPTION' ], null, null, 'DOC DESCRIPTION' ],
		];
	}
	
	#[DataProvider('provideConstructLegacy')]
	public function testConstructLegacy($param, $description, $url, $docDescription) {
		$annotation = new ApiEntity($param);
		$this->assertEquals($annotation->getDescription()   , $description);
		$this->assertEquals($annotation->getUrl()           , $url);
		$this->assertEquals($annotation->getDocDescription(), $docDescription);
	}
	
	public static function provideConstruct() {
		return [
			[ null, null, null ],
			[ 'DESCRIPTION', null, null ],
			[ null, 'http://URL', null ],
			[ null, null, 'DOC DESCRIPTION' ],
		];
	}
	
	#[DataProvider('provideConstruct')]
	public function testConstruct($description, $url, $docDescription) {
		$annotation = new ApiEntity($description, $url, $docDescription);
		$this->assertEquals($annotation->getDescription()   , $description);
		$this->assertEquals($annotation->getUrl()           , $url);
		$this->assertEquals($annotation->getDocDescription(), $docDescription);
	}

}
