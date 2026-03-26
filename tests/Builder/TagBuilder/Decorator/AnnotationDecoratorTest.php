<?php

namespace Test\GollumSF\RestDocBundle\Builder\TagBuilder\Decorator;

use Doctrine\Common\Annotations\Reader;
use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestDocBundle\Annotation\ApiEntity;
use GollumSF\RestDocBundle\Builder\TagBuilder\Decorator\AnnotationDecorator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class AnnotationDecoratorTest extends TestCase {
	
	
	use ReflectionPropertyTrait;
	
	public static function provideGetClassDecorator() {
		return [
			[ new ApiEntity() ],
			[ null ]
		];
	}
	
	#[DataProvider('provideGetClassDecorator')]
	public function testGetClassDecorator($apiEntity) {
		
		$reader = $this->getMockBuilder(Reader::class)->disableOriginalConstructor()->getMock();
		
		$rClass = new \ReflectionClass(\stdClass::class);
		
		$reader
			->expects($this->once())
			->method('getClassAnnotation')
			->willReturnCallback(function ($rClass, $annoClass) use ($apiEntity) {
				$this->assertInstanceOf(\ReflectionClass::class, $rClass);
				$this->assertEquals($rClass->getName(), \stdClass::class);
				$this->assertEquals($annoClass, ApiEntity::class);
				return $apiEntity;
			});
		
		$handler = new AnnotationDecorator(
			$reader
		);
		
		$this->assertEquals(
			$this->reflectionCallMethod($handler, 'getClassDecorator', [ $rClass ]),
			$apiEntity
		);
	}
}
