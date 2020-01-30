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
	
//	public function getXMLName(): string {
//		$xmlName = $this->getClass();
//		if (($index = strrpos('\\', $xmlName)) === false) {
//			$xmlName = substr($xmlName, $index + 1);
//		}
//		return $xmlName;
//	}
// 
//	public function addProperty(ObjectProperty $property): self {
//		$this->properties[$property->getSerializeName()] = $property;
//		return $this;
//	}
//
//	/**
//	 * @return ObjectProperty[]
//	 */
//	public function getProperties(): array {
//		return $this->properties;
//	}
//
//	public function toJson(array $groups = null): array {
//		
//		$properties = array_filter($this->getProperties(), function (ObjectProperty $property) use ($groups) {
//			return
//				!!count($property->getGroups()) &&
//				(
//					$groups === null ||
//					count(array_intersect($groups, $property->getGroups()))
//				)
//				;
//		});
//		if (count($properties) === 0) {
//			return [
//				'type' => 'integer',
//			];
//		}
//		
//		$json = [
//			'type' => $this->getType(),
//			'properties' => array_map(function (ObjectProperty $property) use ($groups) {
//					return $property->toJson($groups);
//			}, $properties),
//			'xml' => [
//				'name' => $this->getXMLName()
//			]
//		];
//		return $json;
//	}
//	public function toJsonRef(): array {
//		$json = [
//			'type' => $this->getType(),
//			'properties' => array_map(
//				function (ObjectProperty $property) {
//					return $property->toJsonRef();
//				}, array_filter($this->getProperties(), function (ObjectProperty $property) {
//					return !!count($property->getGroups());
//				})
//			),
//			'xml' => [
//				'name' => $this->getXMLName()
//			]
//		];
//		return $json;
//	}
}