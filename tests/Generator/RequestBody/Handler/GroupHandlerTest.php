<?php
namespace Test\GollumSF\RestDocBundle\Generator\RequestBody\Handler;

use GollumSF\RestBundle\Metadata\Unserialize\MetadataUnserialize;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\MetadataBuilderInterface;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\Generator\RequestBody\Handler\GroupHandler;
use GollumSF\RestDocBundle\Generator\RequestBody\Handler\RequestBodyPropertiesHandler;
use GollumSF\RestDocBundle\Generator\RequestBody\RequestBodyPropertyCollection;
use GollumSF\RestDocBundle\TypeDiscover\Models\ObjectProperty;
use GollumSF\RestDocBundle\TypeDiscover\Models\ObjectType;
use GollumSF\RestDocBundle\TypeDiscover\Models\TypeInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class GroupHandlerTest extends TestCase {
	
	public function testHasRequestBody() {
		
		$modelBuilder = $this->createMock(ModelBuilderInterface::class);
		
		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata
			->expects($this->exactly(2))
			->method('getUnserializeGroups')
			->willReturnOnConsecutiveCalls(
				[],
				[ 'group1', 'group2' ]
			)
		;
		
		$handler = new GroupHandler($modelBuilder);
		$this->assertFalse($handler->hasRequestBody($metadata, 'GET'));
		$this->assertTrue($handler->hasRequestBody($metadata, 'GET'));
	}

	public static function provideGenerateProperties() {
		return [
			'the unserialize type drives the request body' => [ \ArrayObject::class, \ArrayObject::class ],
			'falls back on the described entity'           => [ null, \stdClass::class ],
		];
	}

	#[DataProvider('provideGenerateProperties')]
	public function testGenerateProperties($unserializeType, $expectedModel) {

		$model = $this->getMockBuilder(ObjectType::class)->disableOriginalConstructor()->getMock();
		$model
			->expects($this->once())
			->method('getPropertiesJson')
			->with(['get', 'group1', 'group2'])
			->willReturn([
				'prop1' => [ 'key' => 'VALUE1' ],
				'prop2' => [ 'key' => 'VALUE2' ],
				'prop3' => [ 'key' => 'VALUE3' ],
			])
		;
		
		$modelBuilder = $this->createMock(ModelBuilderInterface::class);
		$modelBuilder
			->expects($this->once())
			->method('getModel')
			->with($expectedModel)
			->willReturn($model)
		;

		$unserialize = $this->getMockBuilder(MetadataUnserialize::class)->disableOriginalConstructor()->getMock();
		$unserialize
			->method('getType')
			->willReturn($unserializeType)
		;

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata
			->method('getUnserialize')
			->willReturn($unserialize)
		;
		$metadata
			->method('getEntity')
			->willReturn(\stdClass::class)
		;
		$metadata
			->expects($this->once())
			->method('getUnserializeGroups')
			->willReturn([ 'group1', 'group2' ])
		;
		$collection = new RequestBodyPropertyCollection();
		$collection->add('NAME_ORI', [ 'key' =>'VALUE_ORI' ]);
		
		$handler = new GroupHandler($modelBuilder);
		
		$handler->generateProperties($collection, $metadata, 'GET');
		
		$this->assertEquals($collection->toArray(), [
			'NAME_ORI' => [ 'key' =>'VALUE_ORI' ],
			'prop1' => [ 'key' =>'VALUE1' ],
			'prop2' => [ 'key' =>'VALUE2' ],
			'prop3' => [ 'key' =>'VALUE3' ],
		]);
	}

	public function testGeneratePropertiesWithoutUnserializeMetadata() {

		$model = $this->getMockBuilder(ObjectType::class)->disableOriginalConstructor()->getMock();
		$model
			->expects($this->once())
			->method('getPropertiesJson')
			->willReturn([])
		;

		$modelBuilder = $this->createMock(ModelBuilderInterface::class);
		$modelBuilder
			->expects($this->once())
			->method('getModel')
			->with(\stdClass::class)
			->willReturn($model)
		;

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata
			->method('getUnserialize')
			->willReturn(null)
		;
		$metadata
			->method('getEntity')
			->willReturn(\stdClass::class)
		;
		$metadata
			->expects($this->once())
			->method('getUnserializeGroups')
			->willReturn([ 'group1', 'group2' ])
		;

		$collection = new RequestBodyPropertyCollection();
		$handler = new GroupHandler($modelBuilder);

		$handler->generateProperties($collection, $metadata, 'GET');

		$this->assertEquals($collection->toArray(), []);
	}
}
