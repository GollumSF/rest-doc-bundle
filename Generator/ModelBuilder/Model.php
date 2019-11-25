<?php

namespace GollumSF\RestDocBundle\Generator\ModelBuilder;

class Model
{
	/** @var string */
	private $class;

	/** @var ModelProperty[] */
	private $properties = [];

	public function __construct(string $class) {
		$this->class = $class;
	}

	public function getClass(): string {
		return $this->class;
	}

	public function getXMLName(): string {
		$xmlName = $this->getClass();
		if (($index = strrpos('\\', $xmlName)) === false) {
			$xmlName = substr($xmlName, $index + 1);
		}
		return $xmlName;
	}

	public function addProperty(ModelProperty $property): self {
		$this->properties[$property->getSerializeName()] = $property;
		return $this;
	}

	/**
	 * @return ModelProperty[]
	 */
	public function getProperties(): array {
		return $this->properties;
	}

	public function toJson(): array {
		$json = [
			'type' => 'object',
			'properties' => array_map(function (ModelProperty $property){ return $property->toJson(); }, $this->getProperties()),
			'xml' => [
				'name' => $this->getXMLName()
			]
		];
		return $json;
	}
}