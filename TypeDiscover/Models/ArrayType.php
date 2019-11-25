<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Models;

class ArrayType implements TypeInterface {
	
	/** @var TypeInterface */
	private $subType;
	
	public function __construct(TypeInterface $subType) {
		$this->subType = $subType;
	}

	public function getType(): string {
		return 'array';
	}

	public function getSubType(): TypeInterface {
		return $this->subType;
	}

	public function toJson(): array {
		return [
			'type' => $this->getType(),
			'items' => $this->getSubType()->toJson()
		];
	}
}