<?php

namespace Test\GollumSF\RestDocBundle\TypeDiscover\Handler;

use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestDocBundle\Annotation\ApiProperty;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\TypeDiscover\Handler\AttributeHandler;
use PHPUnit\Framework\TestCase;

class DummyClassNull {
	private $dummyProp;
	private function dummyMethod() {}
}

class DummyClassAttribute {
	
	#[ApiProperty('PROPERTY')]
	private $dummyProp;
	#[ApiProperty('METHOD')]
	private function dummyMethod() {}
}

/**
 * @requires PHP 8.0.0
 */
class AttributeHandlerTest extends TestCase {
	
	use ReflectionPropertyTrait;
	
	public function provideGetPropertyDecorator() {
		return [
			[ DummyClassAttribute::class, 'PROPERTY' ],
			[ DummyClassNull::class, null ]
		];
	}
	
	/**
	 * @dataProvider provideGetPropertyDecorator
	 */
	public function testGetPropertyDecorator($class, $name) {
		
		$modelBuilder = $this->getMockBuilder(ModelBuilderInterface::class)->getMockForAbstractClass();
		
		$rClass = new \ReflectionClass($class);
		$rProperty = $rClass->getProperty('dummyProp');
		
		$handler = new AttributeHandler(
			$modelBuilder
		);
		
		/** @var ApiProperty $apiProperty */
		$apiProperty = $this->reflectionCallMethod($handler, 'getPropertyDecorator', [ $rProperty ]);
		if ($name === null) {
			$this->assertNull($apiProperty);
		} else {
			$this->assertEquals($apiProperty->getType(), $name);
		}
	}
	
	public function provideGetMethodDecorator() {
		return [
			[ DummyClassAttribute::class, 'METHOD' ],
			[ DummyClassNull::class, null ]
		];
	}
	
	
	/**
	 * @dataProvider provideGetMethodDecorator
	 */
	public function testGetMethodDecorator($class, $name) {
		
		$modelBuilder = $this->getMockBuilder(ModelBuilderInterface::class)->getMockForAbstractClass();
		
		$rClass = new \ReflectionClass($class);
		$rMethod = $rClass->getMethod('dummyMethod');
		
		$handler = new AttributeHandler(
			$modelBuilder
		);
		
		/** @var ApiProperty $apiProperty */
		$apiProperty = $this->reflectionCallMethod($handler, 'getMethodDecorator', [ $rMethod ]);
		if ($name === null) {
			$this->assertNull($apiProperty);
		} else {
			$this->assertEquals($apiProperty->getType(), $name);
		}
	}

}
