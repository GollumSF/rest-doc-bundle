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

class DoctrineHandlerTest extends TestCase {

	public function providerGetTypeNative() {
		return [
			[ 'integer', 'integer' ],
			[ 'float'  , 'number' ],
			[ 'double' , 'number' ],
			[ 'string' , 'string' ],
			[ 'boolean', 'boolean' ],
		];
	}
	
	/**
	 * @dataProvider providerGetTypeNative
	 */
	public function testGetTypeNative($type, $result) {
		
		$doctrine        = $this->getMockBuilder(ManagerRegistry::class)->disableOriginalConstructor()->getMock();
		$modelBuilder    = $this->getMockForAbstractClass(ModelBuilderInterface::class);
		$manager         = $this->getMockForAbstractClass(ObjectManager::class);
		$metadataFactory = $this->getMockForAbstractClass(ClassMetadataFactory::class);
		$metadata        = $this->getMockBuilder(ClassMetadata::class)->disableOriginalConstructor()->getMockForAbstractClass();
		
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
			->expects($this->at(0))
			->method('getMetadataFactory')
			->willReturn($metadataFactory)
		;
		$manager
			->expects($this->at(1))
			->method('getClassMetadata')
			->with(\stdClass::class)
			->willReturn($metadata)
		;

		$metadata
			->expects($this->at(0))
			->method('hasField')
			->with('TARGET_NAME')
			->willReturn(true)
		;

		$metadata
			->expects($this->at(1))
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
		$modelBuilder    = $this->getMockForAbstractClass(ModelBuilderInterface::class);
		$manager         = $this->getMockForAbstractClass(ObjectManager::class);
		$metadataFactory = $this->getMockForAbstractClass(ClassMetadataFactory::class);
		$metadata        = $this->getMockBuilder(ClassMetadata::class)->disableOriginalConstructor()->getMockForAbstractClass();

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
			->expects($this->at(0))
			->method('getMetadataFactory')
			->willReturn($metadataFactory)
		;
		$manager
			->expects($this->at(1))
			->method('getClassMetadata')
			->with(\stdClass::class)
			->willReturn($metadata)
		;

		$metadata
			->expects($this->at(0))
			->method('hasField')
			->with('TARGET_NAME')
			->willReturn(true)
		;

		$metadata
			->expects($this->at(1))
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
		$modelBuilder    = $this->getMockForAbstractClass(ModelBuilderInterface::class);
		$manager         = $this->getMockForAbstractClass(ObjectManager::class);
		$metadataFactory = $this->getMockForAbstractClass(ClassMetadataFactory::class);
		$metadata        = $this->getMockBuilder(ClassMetadata::class)->disableOriginalConstructor()->getMockForAbstractClass();
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
			->expects($this->at(0))
			->method('getMetadataFactory')
			->willReturn($metadataFactory)
		;
		$manager
			->expects($this->at(1))
			->method('getClassMetadata')
			->with(\stdClass::class)
			->willReturn($metadata)
		;

		$metadata
			->expects($this->at(0))
			->method('hasField')
			->with('TARGET_NAME')
			->willReturn(false)
		;
		$metadata
			->expects($this->at(1))
			->method('hasAssociation')
			->with('TARGET_NAME')
			->willReturn(true)
		;
		$metadata
			->expects($this->at(2))
			->method('getAssociationTargetClass')
			->with('TARGET_NAME')
			->willReturn('SUB_CLASS')
		;
		$metadata
			->expects($this->at(3))
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
		$modelBuilder    = $this->getMockForAbstractClass(ModelBuilderInterface::class);
		$manager         = $this->getMockForAbstractClass(ObjectManager::class);
		$metadataFactory = $this->getMockForAbstractClass(ClassMetadataFactory::class);
		$metadata        = $this->getMockBuilder(ClassMetadata::class)->disableOriginalConstructor()->getMockForAbstractClass();
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
			->expects($this->at(0))
			->method('getMetadataFactory')
			->willReturn($metadataFactory)
		;
		$manager
			->expects($this->at(1))
			->method('getClassMetadata')
			->with(\stdClass::class)
			->willReturn($metadata)
		;

		$metadata
			->expects($this->at(0))
			->method('hasField')
			->with('TARGET_NAME')
			->willReturn(false)
		;
		$metadata
			->expects($this->at(1))
			->method('hasAssociation')
			->with('TARGET_NAME')
			->willReturn(true)
		;
		$metadata
			->expects($this->at(2))
			->method('getAssociationTargetClass')
			->with('TARGET_NAME')
			->willReturn('SUB_CLASS')
		;
		$metadata
			->expects($this->at(3))
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
		$modelBuilder    = $this->getMockForAbstractClass(ModelBuilderInterface::class);

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
		$modelBuilder    = $this->getMockForAbstractClass(ModelBuilderInterface::class);

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

		$modelBuilder    = $this->getMockForAbstractClass(ModelBuilderInterface::class);
		$handler = new DoctrineHandler(
			$modelBuilder
		);

		$result = $handler->getType(\stdClass::class, 'TARGET_NAME');
		$this->assertNull($result);
	}
}