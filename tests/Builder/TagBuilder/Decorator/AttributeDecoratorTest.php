<?php

namespace Test\GollumSF\RestDocBundle\Builder\TagBuilder\Decorator;

use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestDocBundle\Annotation\ApiEntity;
use GollumSF\RestDocBundle\Builder\TagBuilder\Decorator\AttributeDecorator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class AnnoDummyNull {
	public function action() {}
}

#[ApiEntity('CLASS')]
class AnnoDummyClass {
	public function action() {}
}

class AttributeDecoratorTest extends TestCase {
	
	
	use ReflectionPropertyTrait;
	
	public static function provideGetClassDecorator() {
		return [
			[ AnnoDummyClass::class, 'CLASS' ],
			[ AnnoDummyNull::class, null ]
		];
	}
	
	#[DataProvider('provideGetClassDecorator')]
	public function testGetClassDecorator($class, $name) {
		$rClass = new \ReflectionClass($class);
		
		$handler = new AttributeDecorator();
		
		/** @var ApiEntity $apiEntity */
		$apiEntity = $this->reflectionCallMethod($handler, 'getClassDecorator', [ $rClass ]);
		if ($name === null) {
			$this->assertNull($apiEntity);
		} else {
			$this->assertEquals($apiEntity->getDescription(), $name);
		}
	}
}
