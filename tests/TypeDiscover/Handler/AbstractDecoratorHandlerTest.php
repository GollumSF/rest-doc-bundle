<?php

namespace Test\GollumSF\RestDocBundle\TypeDiscover\Handler;

use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestDocBundle\Annotation\ApiProperty;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\TypeDiscover\Handler\AbstractDecoratorHandler;
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

class AbstractDecoratorHandlerMockFromAbstract extends AbstractDecoratorHandler {
	
	/** @var AbstractDecoratorHandler */
	private $mock;
	
	public function __construct(
		ModelBuilderInterface $modelBuilder,
		AbstractDecoratorHandler $mock
	) {
		parent::__construct($modelBuilder);
		$this->mock = $mock;
	}
	
	protected function getPropertyDecorator(\ReflectionProperty $rProperty): ?ApiProperty
	{
		return $this->mock->getPropertyDecorator($rProperty);
	}
	
	protected function getMethodDecorator(\ReflectionMethod $rMethod): ?ApiProperty
	{
		return $this->mock->getMethodDecorator($rMethod);
	}
}

class AnnotationHandlerTestGetType extends AbstractDecoratorHandlerMockFromAbstract {
	public $getType;
	
	protected function createType(string $type): ?TypeInterface {
		return ($this->getType)($type);
	}
}

class AbstractDecoratorHandlerTest extends TestCase {
	
	use ReflectionPropertyTrait;
	
	public function testGetTypeProperty() {

		$mock = $this->getMockBuilder(AbstractDecoratorHandler::class)->disableOriginalConstructor()->getMock();
		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);

		$type = $this->getMockForAbstractClass(TypeInterface::class);

		$annotation = new ApiProperty('TYPE');
		
		$mock
			->expects($this->once())
			->method('getPropertyDecorator')
			->willReturnCallback(function ($rProperty) use ($annotation) {
				$this->assertInstanceOf(\ReflectionProperty::class, $rProperty);
				$this->assertEquals($rProperty->getName(), 'dummyProp');
				return $annotation;
			})
		;

		$decorator = new AnnotationHandlerTestGetType(
			$modelBuilder,
			$mock
		);
		$decorator->getType = function (string $typeStr) use ($type, $annotation) {
			$this->assertEquals($typeStr, $annotation->type);
			return $type;
		};

		$this->assertEquals(
			$decorator->getType(DummyClass::class, 'dummyProp'), $type
		);
	}

	public function testGetTypePropertyCollection() {
		
		$mock = $this->getMockBuilder(AbstractDecoratorHandler::class)->disableOriginalConstructor()->getMock();
		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);

		$type = $this->getMockForAbstractClass(TypeInterface::class);

		$annotation = new ApiProperty('TYPE', true);
		
		$mock
			->expects($this->once())
			->method('getPropertyDecorator')
			->willReturnCallback(function ($rProperty) use ($annotation) {
				$this->assertInstanceOf(\ReflectionProperty::class, $rProperty);
				$this->assertEquals($rProperty->getName(), 'dummyProp');
				return $annotation;
			})
		;

		$decorator = new AnnotationHandlerTestGetType(
			$modelBuilder,
			$mock
		);
		$decorator->getType = function (string $typeStr) use ($type, $annotation) {
			$this->assertEquals($typeStr, $annotation->type);
			return $type;
		};

		/** @var ArrayType $result */
		$result = $decorator->getType(DummyClass::class, 'dummyProp');
		$this->assertInstanceOf(ArrayType::class, $result);
		$this->assertEquals($result->getSubType(), $type);
	}

	public function testGetTypeMethod() {
		
		$mock = $this->getMockBuilder(AbstractDecoratorHandler::class)->disableOriginalConstructor()->getMock();
		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);

		$type = $this->getMockForAbstractClass(TypeInterface::class);

		$annotation = new ApiProperty('TYPE');
		
		$mock
			->expects($this->once())
			->method('getMethodDecorator')
			->willReturnCallback(function ($rMethod) use ($annotation) {
				$this->assertInstanceOf(\ReflectionMethod::class, $rMethod);
				$this->assertEquals($rMethod->getName(), 'dummyMethod');
				return $annotation;
			})
		;

		$decorator = new AnnotationHandlerTestGetType(
			$modelBuilder,
			$mock
		);
		$decorator->getType = function (string $typeStr) use ($type, $annotation) {
			$this->assertEquals($typeStr, $annotation->type);
			return $type;
		};

		$this->assertEquals(
			$decorator->getType(DummyClass::class, 'dummyMethod'), $type
		);
	}

	public function testGetTypeMethodCollection() {
		
		$mock = $this->getMockBuilder(AbstractDecoratorHandler::class)->disableOriginalConstructor()->getMock();
		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);

		$type = $this->getMockForAbstractClass(TypeInterface::class);

		$annotation = new ApiProperty('TYPE', true);
		
		$mock
			->expects($this->once())
			->method('getMethodDecorator')
			->willReturnCallback(function ($rMethod) use ($annotation) {
				$this->assertInstanceOf(\ReflectionMethod::class, $rMethod);
				$this->assertEquals($rMethod->getName(), 'dummyMethod');
				return $annotation;
			})
		;
		
		$decorator = new AnnotationHandlerTestGetType(
			$modelBuilder,
			$mock
		);
		$decorator->getType = function (string $typeStr) use ($type, $annotation) {
			$this->assertEquals($typeStr, $annotation->type);
			return $type;
		};

		/** @var ArrayType $result */
		$result = $decorator->getType(DummyClass::class, 'dummyMethod');
		$this->assertInstanceOf(ArrayType::class, $result);
		$this->assertEquals($result->getSubType(), $type);
	}
	
	public function testGetTypeNoFound() {
		
		$mock = $this->getMockBuilder(AbstractDecoratorHandler::class)->disableOriginalConstructor()->getMock();
		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);
		
		$decorator = new AbstractDecoratorHandlerMockFromAbstract(
			$modelBuilder,
			$mock
		);
		
		$this->assertNull(
			$decorator->getType(\stdClass::class, 'stubName')
		);
	}

	public function testCreateTypeClass() {
		
		$mock = $this->getMockBuilder(AbstractDecoratorHandler::class)->disableOriginalConstructor()->getMock();
		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);

		$decorator = new AbstractDecoratorHandlerMockFromAbstract(
			$modelBuilder,
			$mock
		);

		$type = $this->getMockBuilder(ObjectType::class)->disableOriginalConstructor()->getMock();

		$modelBuilder
			->expects($this->once())
			->method('getModel')
			->willReturn($type)
		;

		$this->assertEquals(
			$this->reflectionCallMethod($decorator, 'createType', [ \stdClass::class ]), $type
		);
	}

	public function testCreateTypeDate() {
		
		$mock = $this->getMockBuilder(AbstractDecoratorHandler::class)->disableOriginalConstructor()->getMock();
		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);

		$decorator = new AbstractDecoratorHandlerMockFromAbstract(
			$modelBuilder,
			$mock
		);

		$modelBuilder
			->expects($this->never())
			->method('getModel')
		;

		$this->assertInstanceOf(
			DateTimeType::class,
			$this->reflectionCallMethod($decorator, 'createType', [ 'datetime' ])
		);
	}
	
	public function provideCreateTypeNative() {
		return [
			[ 'integer', 'integer' ],
			[ 'float'   ,'number' ],
			[ 'double' , 'number' ],
		];
	}

	/**
	 * @dataProvider provideCreateTypeNative
	 */
	public function testCreateTypeNative($type, $result) {
		
		$mock = $this->getMockBuilder(AbstractDecoratorHandler::class)->disableOriginalConstructor()->getMock();
		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);

		$decorator = new AbstractDecoratorHandlerMockFromAbstract(
			$modelBuilder,
			$mock
		);

		$modelBuilder
			->expects($this->never())
			->method('getModel')
		;
		
		/** @var NativeType $returned */
		$returned = $this->reflectionCallMethod($decorator, 'createType', [ $type ]);

		$this->assertInstanceOf(NativeType::class, $returned);
		$this->assertEquals($returned->getType(), $result);
	}
}
