<?php

namespace GollumSF\RestDocBundle\Generator\Parameters;

class ParameterCollection implements \Iterator {

	private $position = 0;
	
	/** @var array[] */
	private $parameters = [];

	public function current(): array {
		return $this->parameters[$this->position];
	}

	public function next(): void {
		++$this->position;
	}

	public function key(): int {
		return $this->position;
	}

	public function valid(): bool {
		return isset($this->parameters[$this->position]);
	}

	public function rewind(): void {
		$this->position = 0;
	}

	public function clear(): self {
		$this->parameters = [];
		return $this;
	}

	public function add(array $parameterData): self {
		if (isset($parameterData['name'])) {
			foreach ($this->parameters as $index => $existing) {
				if (
					isset($existing['name']) &&
					$existing['name'] === $parameterData['name'] &&
					(!isset($parameterData['in']) || !isset($existing['in']) || $parameterData['in'] === $existing['in'])
				) {
					$this->parameters[$index] = array_merge($existing, $parameterData);
					return $this;
				}
			}
		}
		$this->parameters[] = $parameterData;
		return $this;
	}

	public function toArray(): array {
		return $this->parameters;
	}
}