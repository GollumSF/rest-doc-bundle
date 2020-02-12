<?php

namespace Test\GollumSF\RestDocBundle\TypeDiscover\Models;

use GollumSF\RestDocBundle\TypeDiscover\Models\ObjectProperty;
use GollumSF\RestDocBundle\TypeDiscover\Models\ObjectType;
use PHPUnit\Framework\TestCase;

class ObjectTypeTest extends TestCase {
	
	public function testGetter()
	{
		$type = new ObjectType('App\\Entity\\ClassName');

		$this->assertEquals($type->getType(), 'object');
		$this->assertEquals($type->getClass(), 'App\\Entity\\ClassName');
		$this->assertEquals($type->getXMLName(), 'ClassName');
	}

	public function testProperty() {
		$prop1 = $this->getMockBuilder(ObjectProperty::class)->disableOriginalConstructor()->getMock();
		$prop2 = $this->getMockBuilder(ObjectProperty::class)->disableOriginalConstructor()->getMock();
		$prop3 = $this->getMockBuilder(ObjectProperty::class)->disableOriginalConstructor()->getMock();

		$prop1->expects($this->once())->method('getSerializeName')->willReturn('prop1');
		$prop2->expects($this->once())->method('getSerializeName')->willReturn('prop2');
		$prop3->expects($this->once())->method('getSerializeName')->willReturn('prop3');

		$type = new ObjectType('App\\Entity\\ClassName');
		$type->addProperty($prop1);
		$type->addProperty($prop2);
		$type->addProperty($prop3);

		$this->assertEquals($type->getProperties(), [
			'prop1' => $prop1,
			'prop2' => $prop2,
			'prop3' => $prop3,
		]);
	}


	public function testGetPropertiesJson() {
		$type1 = $this->getMockBuilder(ObjectType::class)->disableOriginalConstructor()->getMock();
		$type2 = $this->getMockBuilder(ObjectType::class)->disableOriginalConstructor()->getMock();

		$type1->expects($this->once())->method('toJson')->with([ 'group1', 'group2' ])->willReturn([ 'KEY2' => 'VALUE1' ]);    
		$type2->expects($this->once())->method('toJson')->with([ 'group1', 'group2' ])->willReturn([ 'KEY2' => 'VALUE2' ]);
		
		$prop1 = $this->getMockBuilder(ObjectProperty::class)->disableOriginalConstructor()->getMock();
		$prop2 = $this->getMockBuilder(ObjectProperty::class)->disableOriginalConstructor()->getMock();
		$prop3 = $this->getMockBuilder(ObjectProperty::class)->disableOriginalConstructor()->getMock();

		$prop1->method('getGroups')->willReturn([ 'group1' ]);
		$prop2->method('getGroups')->willReturn([ 'group2', 'group3' ]);
		$prop3->method('getGroups')->willReturn([ 'group3' ]);

		$prop1->method('getType')->willReturn($type1);
		$prop2->method('getType')->willReturn($type2);
		$prop3->expects($this->never())->method('getType');
		
		$prop1->method('getSerializeName')->willReturn('prop1');
		$prop2->method('getSerializeName')->willReturn('prop2');
		$prop3->method('getSerializeName')->willReturn('prop3');

		$type = new ObjectType('App\\Entity\\ClassName');
		$type->addProperty($prop1);
		$type->addProperty($prop2);
		$type->addProperty($prop3);

		$this->assertEquals($type->getPropertiesJson([ 'group1', 'group2' ]), [
			'prop1' => [ 'KEY2' => 'VALUE1' ],
			'prop2' => [ 'KEY2' => 'VALUE2' ],
		]);
	}

	public function providerToJson() {
		return [
			[ null, [
				'type' => 'object',
				'properties' => [
					'prop1' => ['JSON_REF_1'],
					'prop2' => ['JSON_REF_2'],
				],
				'xml' => ['name' => 'ClassName'],
			] ],

			[ [ 'no_group' ], [ 'type' => 'integer' ] ],
			
			[ [ 'group1' ], [
				'type' => 'object',
				'properties' => [
					'prop1' => ['JSON_REF_1'],
				],
				'xml' => ['name' => 'ClassName'],
			] ],
			
			[ [ 'group2' ], [
				'type' => 'object',
				'properties' => [
					'prop2' => ['JSON_REF_2'],
				],
				'xml' => ['name' => 'ClassName'],
			] ],
			[ [ 'group1', 'group2', 'group3' ], [
				'type' => 'object',
				'properties' => [
					'prop1' => ['JSON_REF_1'],
					'prop2' => ['JSON_REF_2'],
				],
				'xml' => ['name' => 'ClassName'],
			] ],
		];
	}
	
	/**
	 * @dataProvider providerToJson
	 */
	public function testToJson($groups, $result)
	{
		$prop1 = $this->getMockBuilder(ObjectProperty::class)->disableOriginalConstructor()->getMock();
		$prop2 = $this->getMockBuilder(ObjectProperty::class)->disableOriginalConstructor()->getMock();
		$prop3 = $this->getMockBuilder(ObjectProperty::class)->disableOriginalConstructor()->getMock();

		$prop1->expects($this->at(0))->method('getSerializeName')->willReturn('prop1');
		$prop2->expects($this->at(0))->method('getSerializeName')->willReturn('prop2');
		$prop3->expects($this->at(0))->method('getSerializeName')->willReturn('prop3');

		$prop1->method('getGroups')->willReturn(['group1']);
		$prop2->method('getGroups')->willReturn(['group2']);
		$prop3->method('getGroups')->willReturn([]);

		$prop1->method('toJson')->willReturn(['JSON_REF_1']);
		$prop2->method('toJson')->willReturn(['JSON_REF_2']);

		$type = new ObjectType('App\\Entity\\ClassName');
		$type->addProperty($prop1);
		$type->addProperty($prop2);
		$type->addProperty($prop3);

		$this->assertEquals($type->toJson($groups), $result);
	}


	public function testToJsonCircular() {

		$type = new ObjectType('App\\Entity\\ClassName');
		$prop = new ObjectProperty('prop1', 'prop_1', $type, [ 'group1' ]);
		$type->addProperty($prop);

		$this->assertEquals($type->toJson(), [
			'type' => 'object',
			'properties' => [
				'prop_1' => [ 'type' => 'integer' ],
			],
			'xml' => ['name' => 'ClassName'],
		]);
	}
	
	public function testToJsonRef() {
		$prop1 = $this->getMockBuilder(ObjectProperty::class)->disableOriginalConstructor()->getMock();
		$prop2 = $this->getMockBuilder(ObjectProperty::class)->disableOriginalConstructor()->getMock();
		$prop3 = $this->getMockBuilder(ObjectProperty::class)->disableOriginalConstructor()->getMock();

		$prop1->expects($this->at(0))->method('getSerializeName')->willReturn('prop1');
		$prop2->expects($this->at(0))->method('getSerializeName')->willReturn('prop2');
		$prop3->expects($this->at(0))->method('getSerializeName')->willReturn('prop3');

		$prop1->expects($this->at(1))->method('getGroups')->willReturn([ 'group1' ]);
		$prop2->expects($this->at(1))->method('getGroups')->willReturn([ 'group2' ]);
		$prop3->expects($this->at(1))->method('getGroups')->willReturn([]);

		$prop1->expects($this->at(2))->method('toJsonRef')->willReturn([ 'JSON_REF_1' ]);
		$prop2->expects($this->at(2))->method('toJsonRef')->willReturn([ 'JSON_REF_2' ]);

		$type = new ObjectType('App\\Entity\\ClassName');
		$type->addProperty($prop1);
		$type->addProperty($prop2);
		$type->addProperty($prop3);

		$this->assertEquals($type->toJsonRef(), [
			'type' => 'object',
			'properties' => [
				'prop1' => [ 'JSON_REF_1' ],
				'prop2' => [ 'JSON_REF_2' ],
			],
			'xml' => [ 'name' => 'ClassName' ],
		]);
	}
}