<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Models;

class NativeType implements TypeInterface {
	
	/** @var string */
	private $type;
	
	public function __construct(string $type) {
		$this->type = $type;
	}

	public function getType(): string {
		return $this->type;
	}

	public function toJson(): array {
		return [
			'type' => $this->getType()
		];
	}
}