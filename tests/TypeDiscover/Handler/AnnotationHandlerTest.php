<?php

namespace Test\GollumSF\RestDocBundle\TypeDiscover\Handler;

use Doctrine\Common\Annotations\Reader;
use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestDocBundle\Annotation\ApiProperty;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\TypeDiscover\Handler\AnnotationHandler;
use PHPUnit\Framework\TestCase;

class DummyClassAnno {
	private $dummyProp;
	private function dummyMethod() {}
}

class AnnotationHandlerTest extends TestCase {
	
	use ReflectionPropertyTrait;
	
	public function provideGetDecorator() {
		return [
			[ new ApiProperty() ],
			[ null ]
		];
	}
	
	/**
	 * @dataProvider provideGetDecorator
	 */
	public function testGetPropertyDecorator($annotation) {
		
		$reader = $this->getMockBuilder(Reader::class)->disableOriginalConstructor()->getMock();
		$modelBuilder = $this->getMockBuilder(ModelBuilderInterface::class)->getMockForAbstractClass();
		
		$rClass = new \ReflectionClass(DummyClassAnno::class);
		$rProperty = $rClass->getProperty('dummyProp');
		
		$reader
			->expects($this->once())
			->method('getPropertyAnnotation')
			->willReturnCallback(function ($rProperty, $annoName) use ($annotation) {
				$this->assertInstanceOf(\ReflectionProperty::class, $rProperty);
				$this->assertEquals($rProperty->getName(), 'dummyProp');
				$this->assertEquals($annoName, ApiProperty::class);
				return $annotation;
			})
		;
		
		$handler = new AnnotationHandler(
			$reader,
			$modelBuilder
		);
		
		$this->assertEquals(
			$this->reflectionCallMethod($handler, 'getPropertyDecorator', [ $rProperty ]),
			$annotation
		);
	}
	
	
	/**
	 * @dataProvider provideGetDecorator
	 */
	public function testGetMethodDecorator($annotation) {
		
		$reader = $this->getMockBuilder(Reader::class)->disableOriginalConstructor()->getMock();
		$modelBuilder = $this->getMockBuilder(ModelBuilderInterface::class)->getMockForAbstractClass();
		
		$rClass = new \ReflectionClass(DummyClassAnno::class);
		$rMethod = $rClass->getMethod('dummyMethod');
		
		$reader
			->expects($this->once())
			->method('getMethodAnnotation')
			->willReturnCallback(function ($rMethod, $annoName) use ($annotation) {
				$this->assertInstanceOf(\ReflectionMethod::class, $rMethod);
				$this->assertEquals($rMethod->getName(), 'dummyMethod');
				$this->assertEquals($annoName, ApiProperty::class);
				return $annotation;
			})
		;
		
		$handler = new AnnotationHandler(
			$reader,
			$modelBuilder
		);
		
		$this->assertEquals(
			$this->reflectionCallMethod($handler, 'getMethodDecorator', [ $rMethod ]),
			$annotation
		);
	}

}
