<?php

namespace Test\GollumSF\RestDocBundle\TypeDiscover\Models;

use GollumSF\RestDocBundle\TypeDiscover\Models\ArrayType;
use GollumSF\RestDocBundle\TypeDiscover\Models\ObjectType;
use GollumSF\RestDocBundle\TypeDiscover\Models\TypeInterface;
use PHPUnit\Framework\TestCase;

class ArrayTypeTest extends TestCase {
	
	public function testGetter() {

		$type = $this->getMockForAbstractClass(TypeInterface::class);
		
		$arrayType1 = new ArrayType();
		$arrayType2 = new ArrayType($type);
		
		$this->assertEquals($arrayType1->getType(), 'array');
		$this->assertEquals($arrayType1->getSubType(), null);
		$this->assertEquals($arrayType2->getSubType(), $type);
	}

	public function testToJson() {

		$type2 = $this->getMockForAbstractClass(TypeInterface::class);
		$type3 = $this->getMockForAbstractClass(TypeInterface::class);

		$type2->expects($this->once())->method('toJson')->with(null)->willReturn([ 'SUB_TYPE' ]);
		$type3->expects($this->once())->method('toJson')->with([ 'GROUP' ])->willReturn([ 'SUB_TYPE' ]);

		$arrayType1 = new ArrayType();
		$arrayType2 = new ArrayType($type2);
		$arrayType3 = new ArrayType($type3);

		$this->assertEquals($arrayType1->toJson(), [
			'type' => 'array',
		]);
		$this->assertEquals($arrayType2->toJson(), [
			'type' => 'array',
			'items' => [ 'SUB_TYPE' ]
		]);
		$this->assertEquals($arrayType3->toJson([ 'GROUP' ]), [
			'type' => 'array',
			'items' => [ 'SUB_TYPE' ]
		]);
	}


	public function testToJsonRef() {

		$typeNative1 = $this->getMockForAbstractClass(TypeInterface::class);
		$typeArray1  = $this->getMockBuilder(ArrayType::class)->disableOriginalConstructor()->getMock();
		$typeNative2 = $this->getMockForAbstractClass(TypeInterface::class);
		$typeArray2  = $this->getMockBuilder(ArrayType::class)->disableOriginalConstructor()->getMock();
		$typeObject  = $this->getMockBuilder(ObjectType::class)->disableOriginalConstructor()->getMock();

		$typeNative1->expects($this->once())->method('toJson'    )->with(null)->willReturn([ 'SUB_TYPE' ]);
		$typeArray1  ->expects($this->once())->method('toJsonRef')->with(null)->willReturn([ 'SUB_TYPE_REF' ]);
		$typeNative2->expects($this->once())->method('toJson'    )->with([ 'GROUP' ])->willReturn([ 'SUB_TYPE' ]);
		$typeArray2  ->expects($this->once())->method('toJsonRef')->with([ 'GROUP' ])->willReturn([ 'SUB_TYPE_REF' ]);
		$typeObject  ->expects($this->once())->method('getClass')->willReturn(\stdClass::class);


		$arrayTypeNull    = new ArrayType();
		$arrayTypeNative1 = new ArrayType($typeNative1);
		$arrayTypeArray1  = new ArrayType($typeArray1);
		$arrayTypeNative2 = new ArrayType($typeNative2);
		$arrayTypeArray2  = new ArrayType($typeArray2);
		$arrayTypeObject = new ArrayType($typeObject);

		$this->assertEquals($arrayTypeNull->toJsonRef(), [
			'type' => 'array',
		]);
		$this->assertEquals($arrayTypeNative1->toJsonRef(), [
			'type' => 'array',
			'items' => [ 'SUB_TYPE' ]
		]);
		$this->assertEquals($arrayTypeArray1->toJsonRef(), [
			'type' => 'array',
			'items' => [ 'SUB_TYPE_REF' ]
		]);
		$this->assertEquals($arrayTypeNative2->toJsonRef([ 'GROUP' ]), [
			'type' => 'array',
			'items' => [ 'SUB_TYPE' ]
		]);
		$this->assertEquals($arrayTypeArray2->toJsonRef([ 'GROUP' ]), [
			'type' => 'array',
			'items' => [ 'SUB_TYPE_REF' ]
		]);
		$this->assertEquals($arrayTypeObject->toJsonRef(), [
			'type' => 'array',
			'items' => [ 
				'$ref' => '#/components/schemas/'.\stdClass::class,
			]
		]);
	}
}