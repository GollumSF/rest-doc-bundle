<?php
namespace Test\GollumSF\RestDocBundle\Generator\Parameters\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\Parameters\Handler\CollectionHandler;
use GollumSF\RestDocBundle\Generator\Parameters\ParameterCollection;
use PHPUnit\Framework\TestCase;

class CollectionHandlerTest extends TestCase {

	public function testGenerateParameter(): void {

		$collection = new ParameterCollection();

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata
			->expects($this->once())
			->method('isCollection')
			->willReturn(true)
		;

		$handler = new CollectionHandler();
		$handler->generateParameter($collection, 'URL', $metadata, 'GET');

		$this->assertEquals($collection->toArray(), [
			[
				'name' => 'limit',
				'in' => 'query',
				'required' => false,
				'type' => 'integer',
				'minimum' => 1,
			], [
				'name' => 'page',
				'in' => 'query',
				'required' => false,
				'type' => 'integer',
			], [
				'name' => 'order',
				'in' => 'query',
				'required' => false,
				'type' => 'string',
			], [
				'name' => 'direction',
				'in' => 'query',
				'required' => false,
				'type' => 'string',
				'enum' => [
					"asc",
					"desc",
				]
			]
		]);
	}

	public function testGenerateParameterFalse(): void {

		$collection = new ParameterCollection();

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata
			->expects($this->once())
			->method('isCollection')
			->willReturn(false)
		;

		$handler = new CollectionHandler();
		$handler->generateParameter($collection, 'URL', $metadata, 'GET');

		$this->assertEquals($collection->toArray(), []);
	}
}