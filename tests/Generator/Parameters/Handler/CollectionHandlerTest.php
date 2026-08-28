<?php
namespace Test\GollumSF\RestDocBundle\Generator\Parameters\Handler;

use GollumSF\RestBundle\Metadata\Sort\MetadataSort;
use GollumSF\RestBundle\Metadata\Sort\MetadataSortable;
use GollumSF\RestBundle\Metadata\Sort\MetadataSortManagerInterface;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\Parameters\Handler\CollectionHandler;
use GollumSF\RestDocBundle\Generator\Parameters\ParameterCollection;
use PHPUnit\Framework\TestCase;

class CollectionHandlerTest extends TestCase {

	private function createHandler(MetadataSort $sort): CollectionHandler {
		$manager = $this->createMock(MetadataSortManagerInterface::class);
		$manager
			->method('getMetadata')
			->with('ENTITY', 'CONTROLLER', 'ACTION')
			->willReturn($sort)
		;
		return new CollectionHandler($manager);
	}

	private function createMetadata(bool $isCollection): Metadata {
		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata->method('isCollection')->willReturn($isCollection);
		$metadata->method('getEntity')->willReturn('ENTITY');
		$metadata->method('getController')->willReturn('CONTROLLER');
		$metadata->method('getAction')->willReturn('ACTION');
		return $metadata;
	}

	/**
	 * Without a declared sortable the order parameter stays a free string, and the
	 * deprecated direction parameter is not documented any more.
	 */
	public function testGenerateParameterWithoutSortable(): void {

		$collection = new ParameterCollection();

		$this
			->createHandler(new MetadataSort())
			->generateParameter($collection, 'URL', $this->createMetadata(true), 'GET')
		;

		$this->assertEquals([
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
				'description' => 'Comma separated sort keys, each optionally suffixed by ":asc" or ":desc".',
			],
		], $collection->toArray());
	}

	public function testGenerateParameterListsTheSortableKeys(): void {

		$collection = new ParameterCollection();

		$sort = new MetadataSort([
			new MetadataSortable('title', 'title'),
			new MetadataSortable('author', 'author.name'),
		]);

		$this
			->createHandler($sort)
			->generateParameter($collection, 'URL', $this->createMetadata(true), 'GET')
		;

		$parameters = $collection->toArray();
		$order = end($parameters);

		$this->assertEquals('order', $order['name']);
		$this->assertEquals(
			'Comma separated sort keys, each optionally suffixed by ":asc" or ":desc". Allowed keys: title, author.',
			$order['description']
		);
		$this->assertEquals('title:desc', $order['example']);
	}

	public function testGenerateParameterFalse(): void {

		$collection = new ParameterCollection();

		$this
			->createHandler(new MetadataSort())
			->generateParameter($collection, 'URL', $this->createMetadata(false), 'GET')
		;

		$this->assertEquals([], $collection->toArray());
	}
}
