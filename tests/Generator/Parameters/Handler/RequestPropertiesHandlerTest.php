<?php
namespace Test\GollumSF\RestDocBundle\Generator\Parameters\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\Parameters\Handler\RequestPropertiesHandler;
use GollumSF\RestDocBundle\Generator\Parameters\ParameterCollection;
use PHPUnit\Framework\TestCase;

class RequestPropertiesHandlerTest extends TestCase {
	
	public function testGenerateParameter() {

		$collection = new ParameterCollection();
		$collection->add([ 'name'=> 'NAME_ORI', 'key' =>'VALUE_ORI' ]);
			
		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata
			->expects($this->once())
			->method('getRequestProperties')
			->willReturn([
				'NAME1' => [ 'key' =>'VALUE1' ],
				'NAME2' => [ 'key' =>'VALUE2' ],
			])
		;

		$handler = new RequestPropertiesHandler();
		$handler->generateParameter($collection, 'URL', $metadata, 'GET');

		$this->assertEquals($collection->toArray(), [
			[ 'name'=> 'NAME_ORI', 'key' =>'VALUE_ORI' ],
			[ 'name'=> 'NAME1', 'key' =>'VALUE1' ],
			[ 'name'=> 'NAME2', 'key' =>'VALUE2' ],
		]);
	}
}