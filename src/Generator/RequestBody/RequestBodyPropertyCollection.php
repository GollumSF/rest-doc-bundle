<?php

namespace GollumSF\RestDocBundle\Generator\RequestBody;

class RequestBodyPropertyCollection implements \Iterator {

	private $position = 0;

	/** @var int[] */
	private $keys = [];
	
	/** @var array[] */
	private $properties = [];

	public function current(): array {
		return $this->properties[$this->key()];
	}

	public function next(): bool {
		++$this->position;
		return $this->valid();
	}

	public function key(): string {
		return $this->keys[$this->position];
	}

	public function valid(): bool {
		return isset($this->keys[$this->position]);
	}

	public function rewind(): self {
		$this->position = 0;
		return $this;
	}

	public function clear(): self {
		$this->keys = [];
		$this->properties = [];
		return $this;
	}

	public function add(string $name, array $parameterData): self {
		if (!in_array($name, $this->keys)) {
			$this->keys[] = $name;	
		}
		$this->properties[$name] = $parameterData;
		return $this;
	}

	public function toArray(): array {
		return $this->properties;
	}
}