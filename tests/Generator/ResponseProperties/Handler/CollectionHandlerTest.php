<?php
namespace Test\GollumSF\RestDocBundle\Generator\Response\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\Generator\ResponseProperties\Handler\CollectionHandler;
use GollumSF\RestDocBundle\Generator\ResponseProperties\ResponsePropertyCollection;
use PHPUnit\Framework\TestCase;

class CollectionHandlerTest extends TestCase {

	public function testGenerateProperties() {

		$collection = new ResponsePropertyCollection();
		$collection->add('NAME_ORI', [ 'key' =>'VALUE_ORI' ]);

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata
			->expects($this->once())
			->method('isCollection')
			->willReturn(true)
		;

		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);

		$handler = new CollectionHandler($modelBuilder);
		$handler->generateResponseProperties($collection, $metadata, 'GET');

		$this->assertEquals($collection->toArray(), [
			'total' => [
				'type' => 'integer'
			],
			'data' => [
				'type' => 'array',
				'items' => [
					'type' => 'object',
					'properties' => [
						'NAME_ORI' => [ 'key' =>'VALUE_ORI' ]
					]
				],
			],
		]);
	}

	public function testGeneratePropertiesNoCollection() {

		$collection = new ResponsePropertyCollection();
		$collection->add('NAME_ORI', [ 'key' =>'VALUE_ORI' ]);

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata
			->expects($this->once())
			->method('isCollection')
			->willReturn(false)
		;

		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);

		$handler = new CollectionHandler($modelBuilder);
		$handler->generateResponseProperties($collection, $metadata, 'GET');

		$this->assertEquals($collection->toArray(), [
			'NAME_ORI' => [ 'key' =>'VALUE_ORI' ]
		]);
	}
}