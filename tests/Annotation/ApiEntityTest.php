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
		$this->assertEquals($annotation->description   , $description);
		$this->assertEquals($annotation->url           , $url);
		$this->assertEquals($annotation->docDescription, $docDescription);
	}
}