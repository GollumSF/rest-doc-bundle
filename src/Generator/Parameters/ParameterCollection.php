<?php

namespace GollumSF\RestDocBundle\Generator\Parameters;

class ParameterCollection implements \Iterator {

	private $position = 0;
	
	/** @var array[] */
	private $parameters = [];

	public function current(): array {
		return $this->parameters[$this->position];
	}

	public function next(): bool {
		++$this->position;
		return $this->valid();
	}

	public function key(): int {
		return $this->position;
	}

	public function valid(): bool {
		return isset($this->parameters[$this->position]);
	}

	public function rewind(): self {
		$this->position = 0;
		return $this;
	}

	public function clear(): self {
		$this->parameters = [];
		return $this;
	}

	public function add(array $parameterData): self {
		$this->parameters[] = $parameterData;
		return $this;
	}

	public function toArray(): array {
		return $this->parameters;
	}
}