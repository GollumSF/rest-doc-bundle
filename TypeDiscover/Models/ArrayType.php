<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Models;

class ArrayType implements TypeInterface {
	
	/** @var TypeInterface */
	private $subType;
	
	public function __construct(?TypeInterface $subType) {
		$this->subType = $subType;
	}

	public function getType(): string {
		return 'array';
	}

	public function getSubType(): TypeInterface {
		return $this->subType;
	}

	public function toJson(array $groups = null): array {
		$json = [
			'type' => $this->getType(),
		];
		if ($this->getSubType()) {
			$json['items'] = $this->getSubType()->toJson($groups);
		}
		return $json;
	}
	public function toJsonRef(array $groups = null): array {
		$json = [
			'type' => $this->getType(),
		];
		$subType = $this->getSubType();
		if ($subType) {
			$type = $this->getSubType();
			if ($type instanceof ObjectType) {
				$json['items'] =  [
					'$ref'=> '#/components/schemas/'.$type->getClass(),
				];
			} else
			if ($type instanceof ArrayType) {
				$json['items'] =   $type->toJsonRef($groups);
			} else {
				$json['items'] =  $type->toJson($groups);
			}
		}
		return $json;
	}
}