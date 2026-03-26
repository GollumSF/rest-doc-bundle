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
use PHPUnit\Framework\Attributes\DataProvider;

class SubDateTime extends \DateTime {
}

class PropertyInfosHandlerTest extends TestCase {

	use ReflectionPropertyTrait;

	private static function usesTypeInfo(): bool {
		return method_exists(PropertyInfoExtractorInterface::class, 'getType');
	}

	public function testGetType() {
		$propertyInfoExtractor = $this->createMock(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->createMock(ModelBuilderInterface::class);

		if (self::usesTypeInfo()) {
			// Symfony 7.1+/8.0+ - uses getType() returning a TypeInfo Type
			$typeInfoType = \Symfony\Component\TypeInfo\Type::int();
			$propertyInfoExtractor
				->expects($this->once())
				->method('getType')
				->with(\stdClass::class, 'TARGET_NAME')
				->willReturn($typeInfoType)
			;
		} else {
			// Symfony 6.4/7.0 - uses getTypes() returning PropertyInfo Type[]
			$legacyType = new \Symfony\Component\PropertyInfo\Type('int');
			$propertyInfoExtractor
				->expects($this->once())
				->method('getTypes')
				->with(\stdClass::class, 'TARGET_NAME')
				->willReturn([$legacyType])
			;
		}

		$handler = new PropertyInfosHandler(
			$propertyInfoExtractor,
			$modelBuilder
		);

		$result = $handler->getType(\stdClass::class, 'TARGET_NAME');
		$this->assertInstanceOf(NativeType::class, $result);
		$this->assertEquals('integer', $result->getType());
	}

	public function testGetTypeNull() {
		$propertyInfoExtractor = $this->createMock(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->createMock(ModelBuilderInterface::class);

		if (self::usesTypeInfo()) {
			$propertyInfoExtractor
				->expects($this->once())
				->method('getType')
				->with(\stdClass::class, 'TARGET_NAME')
				->willReturn(null)
			;
		} else {
			$propertyInfoExtractor
				->expects($this->once())
				->method('getTypes')
				->with(\stdClass::class, 'TARGET_NAME')
				->willReturn(null)
			;
		}

		$handler = new PropertyInfosHandler(
			$propertyInfoExtractor,
			$modelBuilder
		);

		$this->assertNull(
			$handler->getType(\stdClass::class, 'TARGET_NAME')
		);
	}

	public function testGetTypeException() {
		$propertyInfoExtractor = $this->createMock(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->createMock(ModelBuilderInterface::class);

		if (self::usesTypeInfo()) {
			$propertyInfoExtractor
				->expects($this->once())
				->method('getType')
				->with(\stdClass::class, 'TARGET_NAME')
				->willThrowException(new \Exception())
			;
		} else {
			$propertyInfoExtractor
				->expects($this->once())
				->method('getTypes')
				->with(\stdClass::class, 'TARGET_NAME')
				->willThrowException(new \Exception())
			;
		}

		$handler = new PropertyInfosHandler(
			$propertyInfoExtractor,
			$modelBuilder
		);

		$this->assertNull(
			$handler->getType(\stdClass::class, 'TARGET_NAME')
		);
	}

	public static function providerCreateTypeNative() {
		if (self::usesTypeInfo()) {
			return [
				[ \Symfony\Component\TypeInfo\Type::int(), 'integer' ],
				[ \Symfony\Component\TypeInfo\Type::float(), 'number' ],
				[ \Symfony\Component\TypeInfo\Type::string(), 'string' ],
				[ \Symfony\Component\TypeInfo\Type::bool(), 'boolean' ],
			];
		}
		return [
			[ [ new \Symfony\Component\PropertyInfo\Type('int') ], 'integer' ],
			[ [ new \Symfony\Component\PropertyInfo\Type('resource'), new \Symfony\Component\PropertyInfo\Type('int') ], 'integer' ],
			[ [ new \Symfony\Component\PropertyInfo\Type('float') ], 'number' ],
			[ [ new \Symfony\Component\PropertyInfo\Type('string') ], 'string' ],
			[ [ new \Symfony\Component\PropertyInfo\Type('bool') ], 'boolean' ],
		];
	}

	#[DataProvider('providerCreateTypeNative')]
	public function testCreateTypeNative($types, $resultType) {
		$propertyInfoExtractor = $this->createMock(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->createMock(ModelBuilderInterface::class);

		$handler = new PropertyInfosHandler(
			$propertyInfoExtractor,
			$modelBuilder
		);

		if (self::usesTypeInfo()) {
			$result = $this->reflectionCallMethod($handler, 'createTypeFromTypeInfo', [ $types ]);
		} else {
			$result = $this->reflectionCallMethod($handler, 'createTypeLegacy', [ $types ]);
		}
		$this->assertInstanceOf(NativeType::class, $result);
		$this->assertEquals($result->getType(), $resultType);
	}

	public static function providerCreateTypeNull() {
		if (self::usesTypeInfo()) {
			// TypeInfo types that should return null
			return [
				[ \Symfony\Component\TypeInfo\Type::null() ],
			];
		}
		return [
			[ [ new \Symfony\Component\PropertyInfo\Type('resource') ] ],
			[ [ new \Symfony\Component\PropertyInfo\Type('array') ] ],
			[ [ new \Symfony\Component\PropertyInfo\Type('null') ] ],
			[ [ new \Symfony\Component\PropertyInfo\Type('callable') ] ],
			[ [ new \Symfony\Component\PropertyInfo\Type('iterable') ] ],
		];
	}


	#[DataProvider('providerCreateTypeNull')]
	public function testCreateTypeNull($types) {
		$propertyInfoExtractor = $this->createMock(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->createMock(ModelBuilderInterface::class);

		$handler = new PropertyInfosHandler(
			$propertyInfoExtractor,
			$modelBuilder
		);

		if (self::usesTypeInfo()) {
			$this->assertNull(
				$this->reflectionCallMethod($handler, 'createTypeFromTypeInfo', [ $types ])
			);
		} else {
			$this->assertNull(
				$this->reflectionCallMethod($handler, 'createTypeLegacy', [ $types ])
			);
		}
	}

	public function testCreateTypeCollection() {
		$propertyInfoExtractor = $this->createMock(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->createMock(ModelBuilderInterface::class);

		$handler = new PropertyInfosHandler(
			$propertyInfoExtractor,
			$modelBuilder
		);

		if (self::usesTypeInfo()) {
			$type = \Symfony\Component\TypeInfo\Type::list(\Symfony\Component\TypeInfo\Type::int());
			/** @var ArrayType $result */
			$result = $this->reflectionCallMethod($handler, 'createTypeFromTypeInfo', [ $type ]);
		} else {
			$types = [
				new \Symfony\Component\PropertyInfo\Type('array', false, null, true, null, new \Symfony\Component\PropertyInfo\Type('int'))
			];
			/** @var ArrayType $result */
			$result = $this->reflectionCallMethod($handler, 'createTypeLegacy', [ $types ]);
		}

		$this->assertInstanceOf(ArrayType::class, $result);
		$this->assertInstanceOf(NativeType::class, $result->getSubType());
		$this->assertEquals($result->getSubType()->getType(), 'integer');
	}

	public function testCreateTypeObject() {
		$propertyInfoExtractor = $this->createMock(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->createMock(ModelBuilderInterface::class);

		$model = $this->getMockBuilder(ObjectType::class)->disableOriginalConstructor()->getMock();

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

		if (self::usesTypeInfo()) {
			$type = \Symfony\Component\TypeInfo\Type::object(\stdClass::class);
			$result = $this->reflectionCallMethod($handler, 'createTypeFromTypeInfo', [ $type ]);
		} else {
			$types = [
				new \Symfony\Component\PropertyInfo\Type('object', false, \stdClass::class)
			];
			$result = $this->reflectionCallMethod($handler, 'createTypeLegacy', [ $types ]);
		}

		$this->assertEquals($result, $model);
	}

	public function testCreateTypeObjectDateTime() {
		$propertyInfoExtractor = $this->createMock(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->createMock(ModelBuilderInterface::class);

		$modelBuilder
			->expects($this->never())
			->method('getModel')
		;

		$handler = new PropertyInfosHandler(
			$propertyInfoExtractor,
			$modelBuilder
		);

		if (self::usesTypeInfo()) {
			$type = \Symfony\Component\TypeInfo\Type::object(\DateTime::class);
			$result = $this->reflectionCallMethod($handler, 'createTypeFromTypeInfo', [ $type ]);
		} else {
			$types = [
				new \Symfony\Component\PropertyInfo\Type('object', false, \DateTime::class)
			];
			$result = $this->reflectionCallMethod($handler, 'createTypeLegacy', [ $types ]);
		}

		$this->assertInstanceOf(DateTimeType::class, $result);
	}

	public function testCreateTypeObjectSubDateTime() {
		$propertyInfoExtractor = $this->createMock(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->createMock(ModelBuilderInterface::class);

		$modelBuilder
			->expects($this->never())
			->method('getModel')
		;

		$handler = new PropertyInfosHandler(
			$propertyInfoExtractor,
			$modelBuilder
		);

		if (self::usesTypeInfo()) {
			$type = \Symfony\Component\TypeInfo\Type::object(SubDateTime::class);
			$result = $this->reflectionCallMethod($handler, 'createTypeFromTypeInfo', [ $type ]);
		} else {
			$types = [
				new \Symfony\Component\PropertyInfo\Type('object', false, SubDateTime::class)
			];
			$result = $this->reflectionCallMethod($handler, 'createTypeLegacy', [ $types ]);
		}

		$this->assertInstanceOf(DateTimeType::class, $result);
	}
}
