<?php
namespace Test\GollumSF\RestDocBundle\Generator\Parameters\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\Parameters\Handler\UrlHandler;
use GollumSF\RestDocBundle\Generator\Parameters\ParameterCollection;
use PHPUnit\Framework\TestCase;

class UrlHandlerTest extends TestCase {
	
	public function testGenerateParameter(): void {
		$collection = new ParameterCollection();

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		

		$handler = new UrlHandler();
		$handler->generateParameter($collection, '/user/{id}', $metadata, 'GET');
		$this->assertEquals($collection->toArray(), [
			[ 'name'=> 'id', 'in' => 'path' ],
		]);
		$handler->generateParameter($collection, '/user/{id2}/{id3}', $metadata, 'GET');
		
		$this->assertEquals($collection->toArray(), [
			[ 'name'=> 'id', 'in' => 'path' ],
			[ 'name'=> 'id2', 'in' => 'path' ],
			[ 'name'=> 'id3', 'in' => 'path' ],
		]);
	}
}