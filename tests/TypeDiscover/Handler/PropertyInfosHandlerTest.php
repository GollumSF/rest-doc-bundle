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
			$typeInfoType = \Symfony\Component\TypeInfo\Type::int();
			$propertyInfoExtractor
				->expects($this->once())
				->method('getType')
				->with(\stdClass::class, 'TARGET_NAME')
				->willReturn($typeInfoType)
			;
		} else {
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

	// ==========================================
	// Additional tests for TypeInfo code paths
	// ==========================================

	public function testCreateTypeFromTypeInfoNullable() {
		if (!self::usesTypeInfo()) {
			$this->markTestSkipped('Only for Symfony 7.1+ with TypeInfo');
		}

		$propertyInfoExtractor = $this->createMock(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->createMock(ModelBuilderInterface::class);

		$handler = new PropertyInfosHandler(
			$propertyInfoExtractor,
			$modelBuilder
		);

		// ?int => should unwrap to int => NativeType('integer')
		$type = \Symfony\Component\TypeInfo\Type::nullable(\Symfony\Component\TypeInfo\Type::int());
		$result = $this->reflectionCallMethod($handler, 'createTypeFromTypeInfo', [ $type ]);
		$this->assertInstanceOf(NativeType::class, $result);
		$this->assertEquals('integer', $result->getType());
	}

	public function testCreateTypeFromTypeInfoArrayStringFallback() {
		if (!self::usesTypeInfo()) {
			$this->markTestSkipped('Only for Symfony 7.1+ with TypeInfo');
		}

		$propertyInfoExtractor = $this->createMock(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->createMock(ModelBuilderInterface::class);

		$handler = new PropertyInfosHandler(
			$propertyInfoExtractor,
			$modelBuilder
		);

		// Test plain array type (no getCollectionValueType method) via mock
		$mockType = new class {
			public function __toString(): string { return 'array'; }
			public function isNullable(): bool { return false; }
		};

		$result = $this->reflectionCallMethod($handler, 'createTypeFromTypeInfo', [ $mockType ]);
		$this->assertInstanceOf(ArrayType::class, $result);
		$this->assertNull($result->getSubType());
	}

	public function testCreateTypeFromTypeInfoCollectionExceptionPath() {
		if (!self::usesTypeInfo()) {
			$this->markTestSkipped('Only for Symfony 7.1+ with TypeInfo');
		}

		$propertyInfoExtractor = $this->createMock(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->createMock(ModelBuilderInterface::class);

		$handler = new PropertyInfosHandler(
			$propertyInfoExtractor,
			$modelBuilder
		);

		// Mock a type that has getCollectionValueType but throws
		$mockType = new class {
			public function __toString(): string { return 'list<mixed>'; }
			public function isNullable(): bool { return false; }
			public function getCollectionValueType(): never { throw new \LogicException('No value type'); }
		};

		$result = $this->reflectionCallMethod($handler, 'createTypeFromTypeInfo', [ $mockType ]);
		$this->assertInstanceOf(ArrayType::class, $result);
		$this->assertNull($result->getSubType());
	}

	public function testCreateTypeFromTypeInfoNativeAlreadyMapped() {
		if (!self::usesTypeInfo()) {
			$this->markTestSkipped('Only for Symfony 7.1+ with TypeInfo');
		}

		$propertyInfoExtractor = $this->createMock(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->createMock(ModelBuilderInterface::class);

		$handler = new PropertyInfosHandler(
			$propertyInfoExtractor,
			$modelBuilder
		);

		// Test types that match in_array check (already OpenAPI names)
		$mockType = new class {
			public function __toString(): string { return 'integer'; }
			public function isNullable(): bool { return false; }
		};

		$result = $this->reflectionCallMethod($handler, 'createTypeFromTypeInfo', [ $mockType ]);
		$this->assertInstanceOf(NativeType::class, $result);
		$this->assertEquals('integer', $result->getType());
	}

	public function testCreateTypeFromTypeInfoGetClassNameDateTime() {
		if (!self::usesTypeInfo()) {
			$this->markTestSkipped('Only for Symfony 7.1+ with TypeInfo');
		}

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

		// Mock a type whose string representation is not a class but getClassName returns DateTime
		$mockType = new class {
			public function __toString(): string { return 'some_unknown_type'; }
			public function isNullable(): bool { return false; }
			public function getClassName(): string { return \DateTime::class; }
		};

		$result = $this->reflectionCallMethod($handler, 'createTypeFromTypeInfo', [ $mockType ]);
		$this->assertInstanceOf(DateTimeType::class, $result);
	}

	public function testCreateTypeFromTypeInfoGetClassNameSubDateTime() {
		if (!self::usesTypeInfo()) {
			$this->markTestSkipped('Only for Symfony 7.1+ with TypeInfo');
		}

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

		$mockType = new class {
			public function __toString(): string { return 'some_unknown_type'; }
			public function isNullable(): bool { return false; }
			public function getClassName(): string { return SubDateTime::class; }
		};

		$result = $this->reflectionCallMethod($handler, 'createTypeFromTypeInfo', [ $mockType ]);
		$this->assertInstanceOf(DateTimeType::class, $result);
	}

	public function testCreateTypeFromTypeInfoGetClassNameModel() {
		if (!self::usesTypeInfo()) {
			$this->markTestSkipped('Only for Symfony 7.1+ with TypeInfo');
		}

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

		$mockType = new class {
			public function __toString(): string { return 'some_unknown_type'; }
			public function isNullable(): bool { return false; }
			public function getClassName(): string { return \stdClass::class; }
		};

		$result = $this->reflectionCallMethod($handler, 'createTypeFromTypeInfo', [ $mockType ]);
		$this->assertEquals($result, $model);
	}

	public function testCreateTypeFromTypeInfoGetClassNameNull() {
		if (!self::usesTypeInfo()) {
			$this->markTestSkipped('Only for Symfony 7.1+ with TypeInfo');
		}

		$propertyInfoExtractor = $this->createMock(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->createMock(ModelBuilderInterface::class);

		$handler = new PropertyInfosHandler(
			$propertyInfoExtractor,
			$modelBuilder
		);

		// Mock a type whose getClassName returns null
		$mockType = new class {
			public function __toString(): string { return 'some_unknown_type'; }
			public function isNullable(): bool { return false; }
			public function getClassName(): ?string { return null; }
		};

		$result = $this->reflectionCallMethod($handler, 'createTypeFromTypeInfo', [ $mockType ]);
		$this->assertNull($result);
	}

	public function testCreateTypeFromTypeInfoUnknownTypeNoGetClassName() {
		if (!self::usesTypeInfo()) {
			$this->markTestSkipped('Only for Symfony 7.1+ with TypeInfo');
		}

		$propertyInfoExtractor = $this->createMock(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->createMock(ModelBuilderInterface::class);

		$handler = new PropertyInfosHandler(
			$propertyInfoExtractor,
			$modelBuilder
		);

		// Mock a type with unknown string and no getClassName method
		$mockType = new class {
			public function __toString(): string { return 'some_unknown_type'; }
			public function isNullable(): bool { return false; }
		};

		$result = $this->reflectionCallMethod($handler, 'createTypeFromTypeInfo', [ $mockType ]);
		$this->assertNull($result);
	}

	public function testCreateTypeFromTypeInfoCollectionWithNullValueType() {
		if (!self::usesTypeInfo()) {
			$this->markTestSkipped('Only for Symfony 7.1+ with TypeInfo');
		}

		$propertyInfoExtractor = $this->createMock(PropertyInfoExtractorInterface::class);
		$modelBuilder = $this->createMock(ModelBuilderInterface::class);

		$handler = new PropertyInfosHandler(
			$propertyInfoExtractor,
			$modelBuilder
		);

		// Mock a type that has getCollectionValueType returning null
		$mockType = new class {
			public function __toString(): string { return 'list'; }
			public function isNullable(): bool { return false; }
			public function getCollectionValueType(): ?object { return null; }
		};

		$result = $this->reflectionCallMethod($handler, 'createTypeFromTypeInfo', [ $mockType ]);
		$this->assertInstanceOf(ArrayType::class, $result);
		$this->assertNull($result->getSubType());
	}
}
