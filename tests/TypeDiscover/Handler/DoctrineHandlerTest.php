<?php

namespace Test\GollumSF\RestDocBundle\TypeDiscover\Handler;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\Persistence\ObjectManager;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\TypeDiscover\Handler\DoctrineHandler;
use GollumSF\RestDocBundle\TypeDiscover\Models\ArrayType;
use GollumSF\RestDocBundle\TypeDiscover\Models\DateTimeType;
use GollumSF\RestDocBundle\TypeDiscover\Models\NativeType;
use GollumSF\RestDocBundle\TypeDiscover\Models\ObjectType;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class DoctrineHandlerTest extends TestCase {

	public static function providerGetTypeNative() {
		return [
			[ 'integer', 'integer' ],
			[ 'float'  , 'number' ],
			[ 'double' , 'number' ],
			[ 'string' , 'string' ],
			[ 'boolean', 'boolean' ],
		];
	}
	
	#[DataProvider('providerGetTypeNative')]
	public function testGetTypeNative($type, $result) {
		
		$doctrine        = $this->getMockBuilder(ManagerRegistry::class)->disableOriginalConstructor()->getMock();
		$modelBuilder    = $this->createMock(ModelBuilderInterface::class);
		$manager         = $this->createMock(ObjectManager::class);
		$metadataFactory = $this->createMock(ClassMetadataFactory::class);
		$metadata        = $this->createMock(ClassMetadata::class);
		
		$doctrine
			->expects($this->once())
			->method('getManagerForClass')
			->with(\stdClass::class)
			->willReturn($manager)
		;

		$metadataFactory
			->expects($this->once())
			->method('isTransient')
			->with(\stdClass::class)
			->willReturn(false)
		;

		$manager
			->expects($this->once())
			->method('getMetadataFactory')
			->willReturn($metadataFactory)
		;
		$manager
			->expects($this->once())
			->method('getClassMetadata')
			->with(\stdClass::class)
			->willReturn($metadata)
		;

		$metadata
			->expects($this->once())
			->method('hasField')
			->with('TARGET_NAME')
			->willReturn(true)
		;

		$metadata
			->expects($this->once())
			->method('getTypeOfField')
			->with('TARGET_NAME')
			->willReturn($type)
		;
		
		$handler = new DoctrineHandler(
			$modelBuilder
		);
		$handler->setManagerRegistry($doctrine);
		
		/** @var NativeType $result */
		$returned = $handler->getType(\stdClass::class, 'TARGET_NAME');
		$this->assertInstanceOf(NativeType::class, $returned);
		$this->assertEquals($returned->getType(), $result);
	}

	public function testGetTypeDateTime() {

		$doctrine        = $this->getMockBuilder(ManagerRegistry::class)->disableOriginalConstructor()->getMock();
		$modelBuilder    = $this->createMock(ModelBuilderInterface::class);
		$manager         = $this->createMock(ObjectManager::class);
		$metadataFactory = $this->createMock(ClassMetadataFactory::class);
		$metadata        = $this->createMock(ClassMetadata::class);

		$doctrine
			->expects($this->once())
			->method('getManagerForClass')
			->with(\stdClass::class)
			->willReturn($manager)
		;

		$metadataFactory
			->expects($this->once())
			->method('isTransient')
			->with(\stdClass::class)
			->willReturn(false)
		;

		$manager
			->expects($this->once())
			->method('getMetadataFactory')
			->willReturn($metadataFactory)
		;
		$manager
			->expects($this->once())
			->method('getClassMetadata')
			->with(\stdClass::class)
			->willReturn($metadata)
		;

		$metadata
			->expects($this->once())
			->method('hasField')
			->with('TARGET_NAME')
			->willReturn(true)
		;

		$metadata
			->expects($this->once())
			->method('getTypeOfField')
			->with('TARGET_NAME')
			->willReturn('datetime')
		;

		$handler = new DoctrineHandler(
			$modelBuilder
		);
		$handler->setManagerRegistry($doctrine);

		$result = $handler->getType(\stdClass::class, 'TARGET_NAME');
		$this->assertInstanceOf(DateTimeType::class, $result);
	}

	public function testGetTypeAssociation() {

		$doctrine        = $this->getMockBuilder(ManagerRegistry::class)->disableOriginalConstructor()->getMock();
		$modelBuilder    = $this->createMock(ModelBuilderInterface::class);
		$manager         = $this->createMock(ObjectManager::class);
		$metadataFactory = $this->createMock(ClassMetadataFactory::class);
		$metadata        = $this->createMock(ClassMetadata::class);
		$type            = $this->getMockBuilder(ObjectType::class)->disableOriginalConstructor()->getMock();

		$doctrine
			->expects($this->once())
			->method('getManagerForClass')
			->with(\stdClass::class)
			->willReturn($manager)
		;

		$metadataFactory
			->expects($this->once())
			->method('isTransient')
			->with(\stdClass::class)
			->willReturn(false)
		;

		$manager
			->expects($this->once())
			->method('getMetadataFactory')
			->willReturn($metadataFactory)
		;
		$manager
			->expects($this->once())
			->method('getClassMetadata')
			->with(\stdClass::class)
			->willReturn($metadata)
		;

		$metadata
			->expects($this->once())
			->method('hasField')
			->with('TARGET_NAME')
			->willReturn(false)
		;
		$metadata
			->expects($this->once())
			->method('hasAssociation')
			->with('TARGET_NAME')
			->willReturn(true)
		;
		$metadata
			->expects($this->once())
			->method('getAssociationTargetClass')
			->with('TARGET_NAME')
			->willReturn('SUB_CLASS')
		;
		$metadata
			->expects($this->once())
			->method('isCollectionValuedAssociation')
			->with('TARGET_NAME')
			->willReturn(false)
		;

		$modelBuilder
			->expects($this->once())
			->method('getModel')
			->with('SUB_CLASS')
			->willReturn($type)
		;
		$handler = new DoctrineHandler(
			$modelBuilder
		);
		$handler->setManagerRegistry($doctrine);

		$result = $handler->getType(\stdClass::class, 'TARGET_NAME');
		$this->assertEquals($result, $type);
	}

	public function testGetTypeAssociationCollection() {

		$doctrine        = $this->getMockBuilder(ManagerRegistry::class)->disableOriginalConstructor()->getMock();
		$modelBuilder    = $this->createMock(ModelBuilderInterface::class);
		$manager         = $this->createMock(ObjectManager::class);
		$metadataFactory = $this->createMock(ClassMetadataFactory::class);
		$metadata        = $this->createMock(ClassMetadata::class);
		$type            = $this->getMockBuilder(ObjectType::class)->disableOriginalConstructor()->getMock();

		$doctrine
			->expects($this->once())
			->method('getManagerForClass')
			->with(\stdClass::class)
			->willReturn($manager)
		;

		$metadataFactory
			->expects($this->once())
			->method('isTransient')
			->with(\stdClass::class)
			->willReturn(false)
		;

		$manager
			->expects($this->once())
			->method('getMetadataFactory')
			->willReturn($metadataFactory)
		;
		$manager
			->expects($this->once())
			->method('getClassMetadata')
			->with(\stdClass::class)
			->willReturn($metadata)
		;

		$metadata
			->expects($this->once())
			->method('hasField')
			->with('TARGET_NAME')
			->willReturn(false)
		;
		$metadata
			->expects($this->once())
			->method('hasAssociation')
			->with('TARGET_NAME')
			->willReturn(true)
		;
		$metadata
			->expects($this->once())
			->method('getAssociationTargetClass')
			->with('TARGET_NAME')
			->willReturn('SUB_CLASS')
		;
		$metadata
			->expects($this->once())
			->method('isCollectionValuedAssociation')
			->with('TARGET_NAME')
			->willReturn(true)
		;

		$modelBuilder
			->expects($this->once())
			->method('getModel')
			->with('SUB_CLASS')
			->willReturn($type)
		;
		$handler = new DoctrineHandler(
			$modelBuilder
		);
		$handler->setManagerRegistry($doctrine);

		/** @var ArrayType $result */
		$result = $handler->getType(\stdClass::class, 'TARGET_NAME');
		$this->assertInstanceOf(ArrayType::class, $result);
		$this->assertEquals($result->getSubType(), $type);
	}

	public function testGetTypeNoEntity() {

		$doctrine        = $this->getMockBuilder(ManagerRegistry::class)->disableOriginalConstructor()->getMock();
		$modelBuilder    = $this->createMock(ModelBuilderInterface::class);

		$doctrine
			->expects($this->once())
			->method('getManagerForClass')
			->with(\stdClass::class)
			->willReturn(null)
		;

		$handler = new DoctrineHandler(
			$modelBuilder
		);
		$handler->setManagerRegistry($doctrine);

		$result = $handler->getType(\stdClass::class, 'TARGET_NAME');
		$this->assertNull($result);
	}

	public function testGetTypeException() {

		$doctrine        = $this->getMockBuilder(ManagerRegistry::class)->disableOriginalConstructor()->getMock();
		$modelBuilder    = $this->createMock(ModelBuilderInterface::class);

		$doctrine
			->expects($this->once())
			->method('getManagerForClass')
			->with(\stdClass::class)
			->willThrowException(new \Exception())
		;

		$handler = new DoctrineHandler(
			$modelBuilder
		);
		$handler->setManagerRegistry($doctrine);

		$result = $handler->getType(\stdClass::class, 'TARGET_NAME');
		$this->assertNull($result);
	}

	public function testGetTypeNoDoctrine() {

		$modelBuilder    = $this->createMock(ModelBuilderInterface::class);
		$handler = new DoctrineHandler(
			$modelBuilder
		);

		$result = $handler->getType(\stdClass::class, 'TARGET_NAME');
		$this->assertNull($result);
	}
}
