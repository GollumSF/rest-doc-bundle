<?php

namespace Test\GollumSF\RestDocBundle\TypeDiscover\Handler;

use Doctrine\Common\Annotations\Reader;
use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestDocBundle\Annotation\ApiProperty;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\TypeDiscover\Handler\AnnotationHandler;
use GollumSF\RestDocBundle\TypeDiscover\Models\ArrayType;
use GollumSF\RestDocBundle\TypeDiscover\Models\DateTimeType;
use GollumSF\RestDocBundle\TypeDiscover\Models\NativeType;
use GollumSF\RestDocBundle\TypeDiscover\Models\ObjectType;
use GollumSF\RestDocBundle\TypeDiscover\Models\TypeInterface;
use PHPUnit\Framework\TestCase;

class DummyClass {

	private $dummyProp;

	private function dummyMethod() {}
	
}

class AnnotationHandlerTestGetType extends AnnotationHandler {
	public $getType;
	
	protected function createType(string $type): ?TypeInterface {
		return ($this->getType)($type);
	}

}

class AnnotationHandlerTest extends TestCase {
	
	use ReflectionPropertyTrait;
	
	public function testGetTypeProperty() {

		$reader = $this->getMockBuilder(Reader::class)->disableOriginalConstructor()->getMock();
		$modelBuilder = $this->getMockBuilder(ModelBuilderInterface::class)->getMockForAbstractClass();

		$type = $this->getMockForAbstractClass(TypeInterface::class);

		$annotation = new ApiProperty([ 'type' => 'TYPE' ]);

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

		$annotationHandler = new AnnotationHandlerTestGetType(
			$reader,
			$modelBuilder
		);
		$annotationHandler->getType = function (string $typeStr) use ($type, $annotation) {
			$this->assertEquals($typeStr, $annotation->type);
			return $type;
		};

		$this->assertEquals(
			$annotationHandler->getType(DummyClass::class, 'dummyProp'), $type
		);
	}

	public function testGetTypePropertyCollection() {

		$reader = $this->getMockBuilder(Reader::class)->disableOriginalConstructor()->getMock();
		$modelBuilder = $this->getMockBuilder(ModelBuilderInterface::class)->getMockForAbstractClass();

		$type = $this->getMockForAbstractClass(TypeInterface::class);

		$annotation = new ApiProperty([
			'type' => 'TYPE',
			'collection' => true
		]);

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

		$annotationHandler = new AnnotationHandlerTestGetType(
			$reader,
			$modelBuilder
		);
		$annotationHandler->getType = function (string $typeStr) use ($type, $annotation) {
			$this->assertEquals($typeStr, $annotation->type);
			return $type;
		};

		/** @var ArrayType $result */
		$result = $annotationHandler->getType(DummyClass::class, 'dummyProp');
		$this->assertInstanceOf(ArrayType::class, $result);
		$this->assertEquals($result->getSubType(), $type);
	}

	public function testGetTypeMethod() {

		$reader = $this->getMockBuilder(Reader::class)->disableOriginalConstructor()->getMock();
		$modelBuilder = $this->getMockBuilder(ModelBuilderInterface::class)->getMockForAbstractClass();

		$type = $this->getMockForAbstractClass(TypeInterface::class);

		$annotation = new ApiProperty([ 'type' => 'TYPE' ]);

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

		$annotationHandler = new AnnotationHandlerTestGetType(
			$reader,
			$modelBuilder
		);
		$annotationHandler->getType = function (string $typeStr) use ($type, $annotation) {
			$this->assertEquals($typeStr, $annotation->type);
			return $type;
		};

		$this->assertEquals(
			$annotationHandler->getType(DummyClass::class, 'dummyMethod'), $type
		);
	}

	public function testGetTypeMethodCollection() {

		$reader = $this->getMockBuilder(Reader::class)->disableOriginalConstructor()->getMock();
		$modelBuilder = $this->getMockBuilder(ModelBuilderInterface::class)->getMockForAbstractClass();

		$type = $this->getMockForAbstractClass(TypeInterface::class);

		$annotation = new ApiProperty([
			'type' => 'TYPE',
			'collection' => true
		]);

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
		
		$annotationHandler = new AnnotationHandlerTestGetType(
			$reader,
			$modelBuilder
		);
		$annotationHandler->getType = function (string $typeStr) use ($type, $annotation) {
			$this->assertEquals($typeStr, $annotation->type);
			return $type;
		};

		/** @var ArrayType $result */
		$result = $annotationHandler->getType(DummyClass::class, 'dummyMethod');
		$this->assertInstanceOf(ArrayType::class, $result);
		$this->assertEquals($result->getSubType(), $type);
	}
	
	public function testGetTypeNoFound() {

		$reader = $this->getMockBuilder(Reader::class)->disableOriginalConstructor()->getMock();
		$modelBuilder = $this->getMockBuilder(ModelBuilderInterface::class)->getMockForAbstractClass();
		
		$annotationHandler = new AnnotationHandler(
			$reader,
			$modelBuilder
		);
		
		$this->assertNull(
			$annotationHandler->getType(\stdClass::class, 'stubName')
		);
	}

	public function testCreateTypeClass() {

		$reader = $this->getMockBuilder(Reader::class)->disableOriginalConstructor()->getMock();
		$modelBuilder = $this->getMockBuilder(ModelBuilderInterface::class)->getMockForAbstractClass();

		$annotationHandler = new AnnotationHandler(
			$reader,
			$modelBuilder
		);

		$type = $this->getMockBuilder(ObjectType::class)->disableOriginalConstructor()->getMock();

		$modelBuilder
			->expects($this->once())
			->method('getModel')
			->willReturn($type)
		;

		$this->assertEquals(
			$this->reflectionCallMethod($annotationHandler, 'createType', [ \stdClass::class ]), $type
		);
	}

	public function testCreateTypeDate() {

		$reader = $this->getMockBuilder(Reader::class)->disableOriginalConstructor()->getMock();
		$modelBuilder = $this->getMockBuilder(ModelBuilderInterface::class)->getMockForAbstractClass();

		$annotationHandler = new AnnotationHandler(
			$reader,
			$modelBuilder
		);

		$modelBuilder
			->expects($this->never())
			->method('getModel')
		;

		$this->assertInstanceOf(
			DateTimeType::class,
			$this->reflectionCallMethod($annotationHandler, 'createType', [ 'datetime' ])
		);
	}

	public function testCreateTypeNative() {

		$reader = $this->getMockBuilder(Reader::class)->disableOriginalConstructor()->getMock();
		$modelBuilder = $this->getMockBuilder(ModelBuilderInterface::class)->getMockForAbstractClass();

		$annotationHandler = new AnnotationHandler(
			$reader,
			$modelBuilder
		);

		$modelBuilder
			->expects($this->never())
			->method('getModel')
		;
		
		/** @var NativeType $result */
		$result = $this->reflectionCallMethod($annotationHandler, 'createType', [ 'integer' ]);

		$this->assertInstanceOf(NativeType::class, $result);
		$this->assertEquals($result->getType(), 'integer');
	}
}