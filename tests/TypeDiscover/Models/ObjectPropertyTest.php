<?php

namespace Test\GollumSF\RestDocBundle\TypeDiscover\Models;

use GollumSF\RestDocBundle\TypeDiscover\Models\ArrayType;
use GollumSF\RestDocBundle\TypeDiscover\Models\ObjectProperty;
use GollumSF\RestDocBundle\TypeDiscover\Models\ObjectType;
use GollumSF\RestDocBundle\TypeDiscover\Models\TypeInterface;
use PHPUnit\Framework\TestCase;

class ObjectPropertyTest extends TestCase {

	public function testGetter() {
		$type = $this->getMockForAbstractClass(TypeInterface::class);
		$property = new ObjectProperty('NAME', 'prop1', $type, [ 'GROUP1' ]);

		$this->assertEquals($property->getName(), 'NAME');
		$this->assertEquals($property->getSerializeName(), 'prop1');
		$this->assertEquals($property->getGroups(), [ 'GROUP1' ]);
		$this->assertEquals($property->getType(), $type);
	}

	public function testToJson() {
		$type = $this->getMockForAbstractClass(TypeInterface::class);
		$property = new ObjectProperty('NAME', 'prop1', $type, [ 'GROUP1' ]);

		$type
			->expects($this->once())
			->method('toJson')
			->with([ 'GROUP_PARAM' ])
			->willReturn([ 'TO_JSON' ])
		;
		$this->assertEquals($property->toJson([ 'GROUP_PARAM' ]), [ 'TO_JSON' ]);
	}

	public function testToJsonRef() {
		$type1 = $this->getMockForAbstractClass(TypeInterface::class);
		$type2 = $this->getMockBuilder(ObjectType::class)->disableOriginalConstructor()->getMock();
		$type3 = $this->getMockBuilder(ArrayType::class)->disableOriginalConstructor()->getMock();
		$property1 = new ObjectProperty('NAME', 'prop1', $type1, [ 'GROUP1' ]);
		$property2 = new ObjectProperty('NAME', 'prop2', $type2, [ 'GROUP2' ]);
		$property3 = new ObjectProperty('NAME', 'prop3', $type3, [ 'GROUP3' ]);

		$type1
			->expects($this->once())
			->method('toJson')
			->with([ 'GROUP_PARAM1' ])
			->willReturn([ 'TO_JSON1' ])
		;
		$type2
			->expects($this->once())
			->method('getClass')
			->willReturn(\stdClass::class)
		;
		$type3
			->expects($this->once())
			->method('toJsonRef')
			->with([ 'GROUP_PARAM3' ])
			->willReturn([ 'TO_JSON3' ])
		;

		$this->assertEquals($property1->toJsonRef([ 'GROUP_PARAM1' ]), [ 'TO_JSON1' ]);
		$this->assertEquals($property2->toJsonRef([ 'GROUP_PARAM2' ]), [ '$ref' => '#/components/schemas/'.\stdClass::class ]);
		$this->assertEquals($property3->toJsonRef([ 'GROUP_PARAM3' ]), [ 'TO_JSON3' ]);
	}
}