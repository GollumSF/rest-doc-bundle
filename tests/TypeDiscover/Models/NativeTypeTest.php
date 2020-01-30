<?php

namespace Test\GollumSF\RestDocBundle\TypeDiscover\Models;

use GollumSF\RestDocBundle\TypeDiscover\Models\NativeType;
use PHPUnit\Framework\TestCase;

class NativeTypeTest extends TestCase {
	
	public function testGetter() {
		$type = new NativeType('CUSTOM_TYPE');
		$this->assertEquals($type->getType(), 'CUSTOM_TYPE');
		$this->assertEquals($type->toJson(), [
			'type' => 'CUSTOM_TYPE'
		]);
	}
	
}