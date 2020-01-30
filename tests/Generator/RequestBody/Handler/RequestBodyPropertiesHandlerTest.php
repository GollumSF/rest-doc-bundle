<?php
namespace Test\GollumSF\RestDocBundle\Generator\RequestBody\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\RequestBody\Handler\RequestBodyPropertiesHandler;
use GollumSF\RestDocBundle\Generator\RequestBody\RequestBodyPropertyCollection;
use PHPUnit\Framework\TestCase;

class RequestBodyPropertiesHandlerTest extends TestCase {
	
	public function providerHasRequestBody() {
		return [
			[ [ 'NAME' => [ 'key' =>'VALUE' ] ], true ],
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
			->method('getRequestBodyProperties')
			->willReturn($requestProp)
		;

		$handler = new RequestBodyPropertiesHandler();
		$this->assertEquals(
			$handler->hasRequestBody($metadata, 'GET'), $result
		);
	}

	public function testGenerateProperties() {

		$collection = new RequestBodyPropertyCollection();
		$collection->add('NAME_ORI', [ 'key' =>'VALUE_ORI' ]);

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata
			->expects($this->once())
			->method('getRequestBodyProperties')
			->willReturn([
				'NAME1' => [ 'key' =>'VALUE1' ],
				'NAME2' => [ 'key' =>'VALUE2' ],
			])
		;

		$handler = new RequestBodyPropertiesHandler();
		$handler->generateProperties($collection, $metadata, 'GET');
		
		$this->assertEquals($collection->toArray(), [
			'NAME_ORI' => [ 'key' =>'VALUE_ORI' ],
			'NAME1' => [ 'key' =>'VALUE1' ],
			'NAME2'=> [ 'key' =>'VALUE2' ],
		]);
	}
}