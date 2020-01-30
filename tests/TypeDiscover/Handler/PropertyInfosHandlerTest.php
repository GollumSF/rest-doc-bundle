<?php

namespace Test\GollumSF\RestDocBundle\TypeDiscover\Handler;

use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\TypeDiscover\Handler\PropertyInfosHandler;
use GollumSF\RestDocBundle\TypeDiscover\Models\ArrayType;
use GollumSF\RestDocBundle\TypeDiscover\Models\DateTimeType;
use GollumSF\RestDocBundle\TypeDiscover\Models\NativeType;
use GollumSF\RestDocBundle\TypeDiscover\Models\ObjectType;
use GollumSF\RestDocBundle\TypeDiscover\Models\TypeInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

class PropertyInfosHandlerTestGetType extends PropertyInfosHandler {
	
	public $type;
	
	protected function createType(array $types): ?TypeInterface {
		return $this->type;
	}
}

class SubDateTime extends \DateTime {
}

class PropertyInfosHandlerTest extends TestCase {

	use ReflectionPropertyTrait;
	
	public function testGetType() {
		$propertyInfoExtractor = $this->getMockForAbstractClass(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);
		$type = $this->getMockForAbstractClass(TypeInterface::class);

		$propertyInfoExtractor
			->expects($this->once())
			->method('getTypes')
			->with(\stdClass::class, 'TARGET_NAME')
			->willReturn([ 'TYPE' ])
		;

		$handler = new PropertyInfosHandlerTestGetType(
			$propertyInfoExtractor,
			$modelBuilder
		);
		$handler->type = $type;

		$this->assertEquals(
			$handler->getType(\stdClass::class, 'TARGET_NAME'), $type
		);
	}

	public function testGetTypeNull() {
		$propertyInfoExtractor = $this->getMockForAbstractClass(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);

		$propertyInfoExtractor
			->expects($this->once())
			->method('getTypes')
			->with(\stdClass::class, 'TARGET_NAME')
			->willReturn([ 'TYPE' ])
		;

		$handler = new PropertyInfosHandlerTestGetType(
			$propertyInfoExtractor,
			$modelBuilder
		);

		$this->assertNull(
			$handler->getType(\stdClass::class, 'TARGET_NAME')
		);
	}

	public function testGetTypeException() {
		$propertyInfoExtractor = $this->getMockForAbstractClass(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);

		$propertyInfoExtractor
			->expects($this->once())
			->method('getTypes')
			->with(\stdClass::class, 'TARGET_NAME')
			->willThrowException(new \Exception())
		;

		$handler = new PropertyInfosHandlerTestGetType(
			$propertyInfoExtractor,
			$modelBuilder
		);

		$this->assertNull(
			$handler->getType(\stdClass::class, 'TARGET_NAME')
		);
	}
	
	public function providerCreateTypeNative() {
		return [
			[ [ new Type('int') ], 'integer' ],
			[ [ new Type('resource'), new Type('int') ], 'integer' ],
			[ [ new Type('float') ], 'float' ],
			[ [ new Type('string') ], 'string' ],
			[ [ new Type('bool') ], 'boolean' ],
		];
	}

	/**
	 * @dataProvider providerCreateTypeNative
	 */
	public function testCreateTypeNative($types, $resultType) {
		$propertyInfoExtractor = $this->getMockForAbstractClass(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);

		$handler = new PropertyInfosHandler(
			$propertyInfoExtractor,
			$modelBuilder
		);

		$result = $this->reflectionCallMethod($handler, 'createType', [ $types ]);
		$this->assertInstanceOf(NativeType::class, $result);
		$this->assertEquals($result->getType(), $resultType);
	}

	public function providerCreateTypeNull() {
		return [
			[ [ new Type('resource') ] ],
//			[ [ new Type('object') ] ],
			[ [ new Type('array') ] ],
			[ [ new Type('null') ] ],
			[ [ new Type('callable') ] ],
			[ [ new Type('iterable') ] ],
		];
	}


	/**
	 * @dataProvider providerCreateTypeNull
	 */
	public function testCreateTypeNull($types) {
		$propertyInfoExtractor = $this->getMockForAbstractClass(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);

		$handler = new PropertyInfosHandler(
			$propertyInfoExtractor,
			$modelBuilder
		);

		$this->assertNull(
			$this->reflectionCallMethod($handler, 'createType', [ $types ])
		);
	}

	public function testCreateTypeCollection() {
		$propertyInfoExtractor = $this->getMockForAbstractClass(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);

		$types = [
			new Type('array', false, null, true, null, new Type('int'))
		];
		
		$handler = new PropertyInfosHandler(
			$propertyInfoExtractor,
			$modelBuilder
		);

		/** @var ArrayType $result */
		$result = $this->reflectionCallMethod($handler, 'createType', [ $types ]);
		
		
		$this->assertInstanceOf(ArrayType::class, $result);
		$this->assertInstanceOf(NativeType::class, $result->getSubType());
		$this->assertEquals($result->getSubType()->getType(), 'integer');
	}

	public function testCreateTypeObject() {
		$propertyInfoExtractor = $this->getMockForAbstractClass(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);

		$model = $this->getMockBuilder(ObjectType::class)->disableOriginalConstructor()->getMock();

		$types = [
			new Type('object', false, \stdClass::class)
		];

		$modelBuilder
			->expects($this->once())
			->method('getModel')
			->with(\stdClass::class)
			->willReturn($model)
		;

		$handler = new PropertyInfosHandler(
			$propertyInfoExtractor,
			$modelBuilder
		);

		$this->assertEquals(
			$this->reflectionCallMethod($handler, 'createType', [ $types ]), $model
		);
	}

	public function testCreateTypeObjectDateTime() {
		$propertyInfoExtractor = $this->getMockForAbstractClass(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);

		$model = $this->getMockBuilder(ObjectType::class)->disableOriginalConstructor()->getMock();

		$types = [
			new Type('object', false, \DateTime::class)
		];

		$modelBuilder
			->expects($this->never())
			->method('getModel')
		;

		$handler = new PropertyInfosHandler(
			$propertyInfoExtractor,
			$modelBuilder
		);

		$this->assertInstanceOf(
			DateTimeType::class, $this->reflectionCallMethod($handler, 'createType', [ $types ])
		);
	}

	public function testCreateTypeObjectSubDateTime() {
		$propertyInfoExtractor = $this->getMockForAbstractClass(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);

		$model = $this->getMockBuilder(ObjectType::class)->disableOriginalConstructor()->getMock();

		$types = [
			new Type('object', false, SubDateTime::class)
		];

		$modelBuilder
			->expects($this->never())
			->method('getModel')
		;

		$handler = new PropertyInfosHandler(
			$propertyInfoExtractor,
			$modelBuilder
		);

		$this->assertInstanceOf(
			DateTimeType::class, $this->reflectionCallMethod($handler, 'createType', [ $types ])
		);
	}
}