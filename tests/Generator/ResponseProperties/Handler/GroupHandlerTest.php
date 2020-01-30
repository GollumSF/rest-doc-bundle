<?php
namespace Test\GollumSF\RestDocBundle\Generator\ResponseProperties\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\Generator\ResponseProperties\Handler\GroupHandler;
use GollumSF\RestDocBundle\Generator\ResponseProperties\Handler\ResponsePropertiesHandler;
use GollumSF\RestDocBundle\Generator\ResponseProperties\ResponsePropertyCollection;
use GollumSF\RestDocBundle\TypeDiscover\Models\ObjectProperty;
use GollumSF\RestDocBundle\TypeDiscover\Models\ObjectType;
use GollumSF\RestDocBundle\TypeDiscover\Models\TypeInterface;
use PHPUnit\Framework\TestCase;

class GroupHandlerTest extends TestCase {
	public function testGenerateProperties() {

		$prop1 = $this->getMockBuilder(ObjectProperty::class)->disableOriginalConstructor()->getMock();
		$prop2 = $this->getMockBuilder(ObjectProperty::class)->disableOriginalConstructor()->getMock();
		$prop3 = $this->getMockBuilder(ObjectProperty::class)->disableOriginalConstructor()->getMock();
		$prop4 = $this->getMockBuilder(ObjectProperty::class)->disableOriginalConstructor()->getMock();
		$prop5 = $this->getMockBuilder(ObjectProperty::class)->disableOriginalConstructor()->getMock();

		$prop1->expects($this->at(0))->method('getGroups')->willReturn([ 'group1' ]);
		$prop2->expects($this->at(0))->method('getGroups')->willReturn([ 'group2' ]);
		$prop3->expects($this->at(0))->method('getGroups')->willReturn([ 'get', 'group3' ]);
		$prop4->expects($this->at(0))->method('getGroups')->willReturn([ 'group3' ]);
		$prop5->expects($this->at(0))->method('getGroups')->willReturn([]);
		
		$prop1->expects($this->at(1))->method('getSerializeName')->willReturn('prop1');
		$prop2->expects($this->at(1))->method('getSerializeName')->willReturn('prop2');
		$prop3->expects($this->at(1))->method('getSerializeName')->willReturn('prop3');
		$prop4->expects($this->never())->method('getSerializeName');
		$prop5->expects($this->never())->method('getSerializeName');

		$type1 = $this->getMockForAbstractClass(TypeInterface::class);
		$type2 = $this->getMockForAbstractClass(TypeInterface::class);
		$type3 = $this->getMockForAbstractClass(TypeInterface::class);
		
		$prop1->expects($this->at(2))->method('getType')->willReturn($type1);
		$prop2->expects($this->at(2))->method('getType')->willReturn($type2);
		$prop3->expects($this->at(2))->method('getType')->willReturn($type3);

		$type1->expects($this->once())->method('toJson')->with(['get', 'group1', 'group2'])->willReturn([ 'key' => 'VALUE1' ]);
		$type2->expects($this->once())->method('toJson')->with(['get', 'group1', 'group2'])->willReturn([ 'key' => 'VALUE2' ]);
		$type3->expects($this->once())->method('toJson')->with(['get', 'group1', 'group2'])    ->willReturn([ 'key' => 'VALUE3' ]);
		
		$model = $this->getMockBuilder(ObjectType::class)->disableOriginalConstructor()->getMock();
		$model
			->expects($this->once())
			->method('getProperties')
			->willReturn([
				$prop1,
				$prop2,
				$prop3,
				$prop4,
				$prop5,
			])
		;
		
		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);
		$modelBuilder
			->expects($this->once())
			->method('getModel')
			->with(\stdClass::class)
			->willReturn($model)
		;

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata
			->expects($this->at(0))
			->method('getSerializeGroups')
			->willReturn([ 'group1', 'group2' ])
		;
		$metadata
			->expects($this->at(1))
			->method('getEntity')
			->willReturn(\stdClass::class)
		;
		$collection = new ResponsePropertyCollection();
		$collection->add('NAME_ORI', [ 'key' =>'VALUE_ORI' ]);
		
		$handler = new GroupHandler($modelBuilder);
		
		$handler->generateResponseProperties($collection, $metadata, 'GET');
		
		$this->assertEquals($collection->toArray(), [
			'NAME_ORI' => [ 'key' =>'VALUE_ORI' ],
			'prop1' => [ 'key' =>'VALUE1' ],
			'prop2' => [ 'key' =>'VALUE2' ],
			'prop3' => [ 'key' =>'VALUE3' ],
		]);
	}
}