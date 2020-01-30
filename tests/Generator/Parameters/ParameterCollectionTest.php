<?php

namespace Test\GollumSF\RestDocBundle\Generator\Parameters;

use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestDocBundle\Generator\Parameters\ParameterCollection;
use PHPUnit\Framework\TestCase;

class ParameterCollectionTest extends TestCase {
	
	use ReflectionPropertyTrait;
	
	private $collection;
	
	public function setUp(): void {
		$this->collection = new ParameterCollection();
		$this->assertEquals($this->collection->add(['1']), $this->collection);
		$this->assertEquals($this->collection->add(['2']), $this->collection);
		$this->assertEquals($this->collection->add(['3']), $this->collection);
		$this->assertEquals($this->collection->add(['4']), $this->collection);
	}

	public function testCurrentAndNext() {
		$this->assertEquals($this->collection->current(), ['1']); $this->collection->next();
		$this->assertEquals($this->collection->current(), ['2']); $this->collection->next();
		$this->assertEquals($this->collection->current(), ['3']); $this->collection->next();
		$this->assertEquals($this->collection->current(), ['4']);
	}

	public function testNextAndValid() {
		$this->assertEquals($this->collection->key(), 0); $this->assertEquals($this->collection->next(), true);
		$this->assertEquals($this->collection->key(), 1); $this->assertEquals($this->collection->next(), true);
		$this->assertEquals($this->collection->key(), 2); $this->assertEquals($this->collection->next(), true);
		$this->assertEquals($this->collection->key(), 3); $this->assertEquals($this->collection->next(), false);
	}


	public function testRewind(): self {
		$this->collection->next();
		$this->collection->next();
		$this->collection->next();
		$this->assertEquals($this->collection->rewind(), $this->collection);
		$this->assertEquals($this->collection->key(), 0);
		return $this;
	}

	public function testClear() {
		$this->assertEquals($this->collection->clear(), $this->collection);
		$this->assertEquals($this->reflectionGetValue($this->collection, 'position'), 0);
		$this->assertEquals($this->reflectionGetValue($this->collection, 'parameters'), []);
	}

	public function testAdd() {
		$this->assertEquals($this->reflectionGetValue($this->collection, 'parameters'), [
			['1'],
			['2'],
			['3'],
			['4'],
		]);
	}

	public function testToArray() {
		$this->assertEquals($this->collection->toArray(), [
			['1'],
			['2'],
			['3'],
			['4'],
		]);
	}
}