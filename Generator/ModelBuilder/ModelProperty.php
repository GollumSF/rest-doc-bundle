<?php

namespace GollumSF\RestDocBundle\Generator\ModelBuilder;

class ModelProperty
{
	/** @var string */
	private $name;
	
	/** @var string */
	private $serializeName;

	/** @var string */
	private $type;
	
	/** @var string[] */
	private $groups;

	public function __construct(string $name, string $serializeName, string $type, array $groups)
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

	public function getType(): string {
		return $this->type;
	}

	public function getGroups(): array {
		return $this->groups;
	}
	
	public function toJson(): array {
		return [
			'type' => $this->getType()
		];
	}
}