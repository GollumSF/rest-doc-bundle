<?php
namespace Test\GollumSF\RestDocBundle\Generator\RequestBody\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\RequestBody\Handler\RequestBodyPropertiesHandler;
use GollumSF\RestDocBundle\Generator\RequestBody\RequestBodyPropertyCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class RequestBodyPropertiesHandlerTest extends TestCase {
	
	public static function providerHasRequestBody() {
		return [
			[ [ 'body' => [ 'properties' => [ 'NAME' => [ 'key' =>'VALUE' ] ] ] ], true ],
			[ [ 'body' => [ 'properties' => [] ] ], false ],
			[ [ 'body' => [ 'properties' => 'NOT_ARRAY' ] ], false ],
			[ [ 'body' => 'NOT_ARRAY' ], false ],
			[ [ 'body' => [] ], false ],
			[ [], false ],
		];
	}

	#[DataProvider('providerHasRequestBody')]
	public function testHasRequestBody($requestProp, $result) {

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata
			->expects($this->once())
			->method('getRequest')
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
			->method('getRequest')
			->willReturn([
				'body' => [ 
					'properties' => [
						'NAME1' => [ 'key' =>'VALUE1' ],
						'NAME2' => [ 'key' =>'VALUE2' ],
					]
				]
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