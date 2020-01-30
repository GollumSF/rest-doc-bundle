<?php

namespace Test\GollumSF\RestDocBundle\TypeDiscover\Models;

use GollumSF\RestDocBundle\TypeDiscover\Models\DateTimeType;
use PHPUnit\Framework\TestCase;

class DateTimeTypeTest extends TestCase {
	
	public function testGetType() {
		$dateTimeType = new DateTimeType();
		$this->assertEquals($dateTimeType->getType(), 'string');
	}
	
	public function testToJson() {
		$dateTimeType = new DateTimeType();
		
		$result = $dateTimeType->toJson();

		$this->assertEquals($result['type'], 'string');
		$this->assertTrue(\DateTime::createFromFormat(\DateTime::RFC3339, $result['example']) !== false);
	}
}