<?php
namespace Test\GollumSF\RestDocBundle\Generator\Response\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\Generator\ResponseProperties\Handler\ResponsePropertiesHandler;
use GollumSF\RestDocBundle\Generator\ResponseProperties\ResponsePropertyCollection;
use PHPUnit\Framework\TestCase;

class ResponsePropertiesHandlerTest extends TestCase {

	public function testGenerateProperties() {

		$collection = new ResponsePropertyCollection();
		$collection->add('NAME_ORI', [ 'key' =>'VALUE_ORI' ]);

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata
			->expects($this->exactly(2))
			->method('getResponseBodyProperties')
			->willReturn([
				'NAME1' => [ 'key' =>'VALUE1' ],
				'NAME2' => [ 'key' =>'VALUE2' ],
			])
		;

		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);

		$handler = new ResponsePropertiesHandler($modelBuilder);
		$handler->generateResponseProperties($collection, $metadata, 'GET');

		$this->assertEquals($collection->toArray(), [
			'NAME_ORI' => [ 'key' =>'VALUE_ORI' ],
			'NAME1' => [ 'key' =>'VALUE1' ],
			'NAME2'=> [ 'key' =>'VALUE2' ],
		]);
	}

	public function testGeneratePropertiesNo() {

		$collection = new ResponsePropertyCollection();
		$collection->add('NAME_ORI', [ 'key' =>'VALUE_ORI' ]);

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata
			->expects($this->once())
			->method('getResponseBodyProperties')
			->willReturn([])
		;

		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);

		$handler = new ResponsePropertiesHandler($modelBuilder);
		$handler->generateResponseProperties($collection, $metadata, 'GET');

		$this->assertEquals($collection->toArray(), [
			'NAME_ORI' => [ 'key' =>'VALUE_ORI' ],
		]);
	}
}