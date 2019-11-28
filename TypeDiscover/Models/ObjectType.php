<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Models;

class ObjectType implements TypeInterface {
	
	/** @var string */
	private $class;

	/** @var ObjectProperty[] */
	private $properties = [];

	public function __construct(string $class) {
		$this->class = $class;
	}

	public function getType(): string {
		return 'object';
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
 
	public function addProperty(ObjectProperty $property): self {
		$this->properties[$property->getSerializeName()] = $property;
		return $this;
	}

	/**
	 * @return ObjectProperty[]
	 */
	public function getProperties(): array {
		return $this->properties;
	}

	public function toJson(): array {
		$json = [
			'type' => $this->getType(),
			'properties' => array_map(
				function (ObjectProperty $property){
					return $property->toJson();
				}, array_filter($this->getProperties(), function (ObjectProperty $property) {
					return !!count($property->getGroups());
				})
			),
			'xml' => [
				'name' => $this->getXMLName()
			]
		];
		return $json;
	}
}