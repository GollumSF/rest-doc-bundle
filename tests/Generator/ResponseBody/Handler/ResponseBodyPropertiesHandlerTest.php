<?php
namespace Test\GollumSF\RestDocBundle\Generator\ResponseBody\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\Generator\ResponseBody\Handler\ResponseBodyPropertiesHandler;
use GollumSF\RestDocBundle\Generator\ResponseBody\ResponseBodyPropertyCollection;
use PHPUnit\Framework\TestCase;

class ResponseBodyPropertiesHandlerTest extends TestCase {

	public function providerHasRequestBody() {
		return [
			[ [ 'body' => [ 'properties' => [ 'NAME' => [ 'key' =>'VALUE' ] ] ] ], true ],
			[ [ 'body' => [ 'properties' => [] ] ], false ],
			[ [ 'body' => [ 'properties' => 'NOT_ARRAY' ] ], false ],
			[ [ 'body' => 'NOT_ARRAY' ], false ],
			[ [ 'body' => [] ], false ],
			[ [], false ],
		];
	}

	/**
	 * @dataProvider providerHasRequestBody
	 */
	public function testHasRequestBody($requestProp, $result) {

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata
			->expects($this->once())
			->method('getResponse')
			->willReturn($requestProp)
		;

		$handler = new ResponseBodyPropertiesHandler();
		$this->assertEquals(
			$handler->hasResponseBody($metadata, 'GET'), $result
		);
	}

	public function testGenerateProperties() {

		$collection = new ResponseBodyPropertyCollection();
		$collection->add('NAME_ORI', [ 'key' =>'VALUE_ORI' ]);

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata
			->expects($this->once())
			->method('getResponse')
			->willReturn([
				'body' => [ 
					'properties' => [
						'NAME1' => [ 'key' =>'VALUE1' ],
						'NAME2' => [ 'key' =>'VALUE2' ],
					]
				]
			])
		;

		$modelBuilder = $this->getMockForAbstractClass(ModelBuilderInterface::class);

		$handler = new ResponseBodyPropertiesHandler($modelBuilder);
		$handler->generateProperties($collection, $metadata, 'GET');

		$this->assertEquals($collection->toArray(), [
			'NAME_ORI' => [ 'key' =>'VALUE_ORI' ],
			'NAME1' => [ 'key' =>'VALUE1' ],
			'NAME2'=> [ 'key' =>'VALUE2' ],
		]);
	}
}