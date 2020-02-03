<?php
namespace Test\GollumSF\RestDocBundle\Generator\ResponseBody\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\Generator\ResponseBody\Handler\CollectionHandler;
use GollumSF\RestDocBundle\Generator\ResponseBody\ResponseBodyPropertyCollection;
use PHPUnit\Framework\TestCase;

class CollectionHandlerTest extends TestCase {

	public function testHasResponsetBody() {

		$metadata1 = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata1
			->expects($this->once())
			->method('isCollection')
			->willReturn(true)
		;
		$metadata2 = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata2
			->expects($this->once())
			->method('isCollection')
			->willReturn(false)
		;

		$handler = new CollectionHandler();
		$this->assertTrue($handler->hasResponseBody($metadata1, 'GET'));
		$this->assertFalse($handler->hasResponseBody($metadata2, 'GET'));
	}

	public function testGenerateProperties() {

		$collection = new ResponseBodyPropertyCollection();
		$collection->add('NAME_ORI', [ 'key' =>'VALUE_ORI' ]);

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();

		$handler = new CollectionHandler();
		$handler->generateProperties($collection, $metadata, 'GET');

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
}