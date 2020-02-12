<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Models;

class ObjectType implements TypeInterface {

	private static $circularRef = [];

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
		if (($index = strrpos($xmlName, '\\')) !== false) {
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

	public function getPropertiesJson(array $groups = null) {
		/** @var ObjectProperty[] $properties */
		$properties = array_filter($this->getProperties(), function (ObjectProperty $property) use ($groups) {
			return
				!!count($property->getGroups()) &&
				(
					$groups === null ||
					count(array_intersect($groups, $property->getGroups()))
				)
				;
		});

		$json = [];
		ObjectType::$circularRef[] = $this->getClass();
		foreach ($properties as $property) {
			$json[$property->getSerializeName()] = $property->getType()->toJson($groups);
		}
		array_pop(ObjectType::$circularRef);
		return $json;
	}

	public function toJson(array $groups = null): array {
		if (in_array($this->getClass(), ObjectType::$circularRef)) {
			return [
				'type' => 'integer',
			];
		}
		$properties = array_filter($this->getProperties(), function (ObjectProperty $property) use ($groups) {
			return
				!!count($property->getGroups()) &&
				(
					$groups === null ||
					count(array_intersect($groups, $property->getGroups()))
				)
				;
		});

		if (count($properties) === 0) {
			return [
				'type' => 'integer',
			];
		}

		ObjectType::$circularRef[] = $this->getClass();
		$json = [
			'type' => $this->getType(),
			'properties' => array_map(function (ObjectProperty $property) use ($groups) {
				return $property->toJson($groups);
			}, $properties),
			'xml' => [
				'name' => $this->getXMLName()
			]
		];
		array_pop(ObjectType::$circularRef);
		return $json;
	}

	public function toJsonRef(): array {
		$json = [
			'type' => $this->getType(),
			'properties' => array_map(
				function (ObjectProperty $property) {
					return $property->toJsonRef();
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