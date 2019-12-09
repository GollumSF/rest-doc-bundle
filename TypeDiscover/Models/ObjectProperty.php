<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Models;

class ObjectProperty {
	
	/** @var string */
	private $name;
	
	/** @var string */
	private $serializeName;

	/** @var TypeInterface */
	private $type;
	
	/** @var string[] */
	private $groups;

	public function __construct(string $name, string $serializeName, TypeInterface $type, array $groups)
	{
		$this->name = $name;
		$this->serializeName = $serializeName;
		$this->type = $type;
		$this->groups = $groups;
	}

	public function getName(): string {
		return $this->name;
	}

	public function getSerializeName(): string {
		return $this->serializeName;
	}

	public function getType(): TypeInterface {
		return $this->type;
	}

	public function getGroups(): array {
		return $this->groups;
	}
	
	public function toJson(array $groups = null): array {
		return $this->getType()->toJson($groups);
	}
	public function toJsonRef(array $groups = null): array {

		$type = $this->getType();
		if ($type instanceof ObjectType) {
			return [
				'$ref'=> '#/components/schemas/'.$type->getClass(),
			];
		}
		if ($type instanceof ArrayType) {
			return $type->toJsonRef($groups);
		}
		return $type->toJson($groups);
	}
}