<?php
namespace Test\GollumSF\RestDocBundle\Generator\ResponseBody\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\Generator\ResponseBody\Handler\GroupHandler;
use GollumSF\RestDocBundle\Generator\ResponseBody\Handler\ResponseBodyPropertiesHandler;
use GollumSF\RestDocBundle\Generator\ResponseBody\ResponseBodyPropertyCollection;
use GollumSF\RestDocBundle\TypeDiscover\Models\ObjectProperty;
use GollumSF\RestDocBundle\TypeDiscover\Models\ObjectType;
use GollumSF\RestDocBundle\TypeDiscover\Models\TypeInterface;
use PHPUnit\Framework\TestCase;

class GroupHandlerTest extends TestCase {

	public function testHasResponseBody() {

		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata
			->expects($this->at(0))
			->method('getSerializeGroups')
			->willReturn([])
		;
		$metadata
			->expects($this->at(1))
			->method('getSerializeGroups')
			->willReturn([ 'group1', 'group2' ])
		;

		$handler = new GroupHandler($modelBuilder);
		$this->assertFalse($handler->hasResponseBody($metadata, 'GET'));
		$this->assertTrue($handler->hasResponseBody($metadata, 'GET'));
	}
	
	public function testGenerateProperties() {

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
		$collection = new ResponseBodyPropertyCollection();
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
}