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
		$this->assertEquals($this->collection->key(), 0); $this->collection->next(); $this->assertTrue($this->collection->valid());
		$this->assertEquals($this->collection->key(), 1); $this->collection->next(); $this->assertTrue($this->collection->valid());
		$this->assertEquals($this->collection->key(), 2); $this->collection->next(); $this->assertTrue($this->collection->valid());
		$this->assertEquals($this->collection->key(), 3); $this->collection->next(); $this->assertFalse($this->collection->valid());
	}


	public function testRewind(): void {
		$this->collection->next();
		$this->collection->next();
		$this->collection->next();
		$this->collection->rewind();
		$this->assertEquals($this->collection->key(), 0);
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

	public function testAddMergeByNameWithoutIn() {
		$collection = new ParameterCollection();
		$collection->add(['name' => 'domain', 'in' => 'path']);
		$collection->add(['name' => 'domain', 'schema' => ['type' => 'string', 'enum' => ['front', 'indications']]]);

		$this->assertEquals($collection->toArray(), [
			['name' => 'domain', 'in' => 'path', 'schema' => ['type' => 'string', 'enum' => ['front', 'indications']]],
		]);
	}

	public function testAddMergeByNameWithSameIn() {
		$collection = new ParameterCollection();
		$collection->add(['name' => 'domain', 'in' => 'path']);
		$collection->add(['name' => 'domain', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']]);

		$this->assertEquals($collection->toArray(), [
			['name' => 'domain', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']],
		]);
	}

	public function testAddNoMergeWithDifferentIn() {
		$collection = new ParameterCollection();
		$collection->add(['name' => 'token', 'in' => 'path']);
		$collection->add(['name' => 'token', 'in' => 'query']);

		$this->assertEquals($collection->toArray(), [
			['name' => 'token', 'in' => 'path'],
			['name' => 'token', 'in' => 'query'],
		]);
	}

	public function testAddNoMergeWithoutName() {
		$collection = new ParameterCollection();
		$collection->add(['key' => 'value1']);
		$collection->add(['key' => 'value2']);

		$this->assertEquals($collection->toArray(), [
			['key' => 'value1'],
			['key' => 'value2'],
		]);
	}

	public function testAddMergePreservesPosition() {
		$collection = new ParameterCollection();
		$collection->add(['name' => 'id', 'in' => 'path']);
		$collection->add(['name' => 'domain', 'in' => 'path']);
		$collection->add(['name' => 'domain', 'schema' => ['type' => 'string', 'enum' => ['a', 'b']]]);

		$this->assertEquals($collection->toArray(), [
			['name' => 'id', 'in' => 'path'],
			['name' => 'domain', 'in' => 'path', 'schema' => ['type' => 'string', 'enum' => ['a', 'b']]],
		]);
	}

	public function testAddMergeOverridesValues() {
		$collection = new ParameterCollection();
		$collection->add(['name' => 'locale', 'in' => 'path', 'required' => false]);
		$collection->add(['name' => 'locale', 'required' => true]);

		$this->assertEquals($collection->toArray(), [
			['name' => 'locale', 'in' => 'path', 'required' => true],
		]);
	}
}