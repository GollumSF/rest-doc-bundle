<?php

namespace Test\GollumSF\RestDocBundle\Annotation;

use GollumSF\RestDocBundle\Annotation\ApiEntity;
use PHPUnit\Framework\TestCase;

class ApiEntityTest extends TestCase {

	public function provideConstruct() {
		return [
			[ [], null, null, null ],
			[ [ 'description' => 'DESCRIPTION' ], 'DESCRIPTION', null, null ],
			[ [ 'url' => 'http://URL' ], null, 'http://URL', null ],
			[ [ 'docDescription' => 'DOC DESCRIPTION' ], null, null, 'DOC DESCRIPTION' ],
		];
	}

	/**
	 * @dataProvider provideConstruct
	 */
	public function testConstruct($param, $description, $url, $docDescription) {
		$annotation = new ApiEntity($param);
		$this->assertEquals($annotation->getDescription()   , $description);
		$this->assertEquals($annotation->getUrl()           , $url);
		$this->assertEquals($annotation->getDocDescription(), $docDescription);
	}
	
	public function testConstructException() {
		$this->expectException(\RuntimeException::class);
		$annotation = new ApiEntity(['bad' => 'value']);
	}
}