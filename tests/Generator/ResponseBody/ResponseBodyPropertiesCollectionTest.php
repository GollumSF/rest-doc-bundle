<?php

namespace Test\GollumSF\RestDocBundle\Generator\ResponseBody;

use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestDocBundle\Generator\ResponseBody\ResponseBodyPropertyCollection;
use PHPUnit\Framework\TestCase;

class ResponseBodyPropertiesCollectionTest extends TestCase {
	
	use ReflectionPropertyTrait;
	
	private $collection;
	
	public function setUp(): void {
		$this->collection = new ResponseBodyPropertyCollection();
		$this->assertEquals($this->collection->add('a', ['1']), $this->collection);
		$this->assertEquals($this->collection->add('b', ['2']), $this->collection);
		$this->assertEquals($this->collection->add('c', ['3']), $this->collection);
		$this->assertEquals($this->collection->add('d', ['4']), $this->collection);
	}

	public function testCurrentAndNext() {
		$this->assertEquals($this->collection->current(), ['1']); $this->collection->next();
		$this->assertEquals($this->collection->current(), ['2']); $this->collection->next();
		$this->assertEquals($this->collection->current(), ['3']); $this->collection->next();
		$this->assertEquals($this->collection->current(), ['4']);
	}

	public function testNextAndValid() {
		$this->assertEquals($this->collection->key(), 'a'); $this->assertEquals($this->collection->next(), true);
		$this->assertEquals($this->collection->key(), 'b'); $this->assertEquals($this->collection->next(), true);
		$this->assertEquals($this->collection->key(), 'c'); $this->assertEquals($this->collection->next(), true);
		$this->assertEquals($this->collection->key(), 'd'); $this->assertEquals($this->collection->next(), false);
	}


	public function testRewind(): self {
		$this->collection->next();
		$this->collection->next();
		$this->collection->next();
		$this->assertEquals($this->collection->rewind(), $this->collection);
		$this->assertEquals($this->collection->key(), 'a');
		return $this;
	}

	public function testClear() {
		$this->assertEquals($this->collection->clear(), $this->collection);
		$this->assertEquals($this->reflectionGetValue($this->collection, 'position'), 0);
		$this->assertEquals($this->reflectionGetValue($this->collection, 'keys'), []);
		$this->assertEquals($this->reflectionGetValue($this->collection, 'properties'), []);
	}

	public function testAdd() {
		$this->assertEquals($this->reflectionGetValue($this->collection, 'keys'), [
			'a',
			'b',
			'c',
			'd',
		]);
		$this->assertEquals($this->reflectionGetValue($this->collection, 'properties'), [
			'a' => ['1'],
			'b' => ['2'],
			'c' => ['3'],
			'd' => ['4'],
		]);
	}

	public function testToArray() {
		$this->assertEquals($this->collection->toArray(), [
			'a' => ['1'],
			'b' => ['2'],
			'c' => ['3'],
			'd' => ['4'],
		]);
	}
}