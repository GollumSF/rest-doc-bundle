<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Handler;

use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\TypeDiscover\Models\ArrayType;
use GollumSF\RestDocBundle\TypeDiscover\Models\DateTimeType;
use GollumSF\RestDocBundle\TypeDiscover\Models\NativeType;
use GollumSF\RestDocBundle\TypeDiscover\Models\TypeInterface;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;

class PropertyInfosHandler implements HandlerInterface {

	/** @var PropertyInfoExtractorInterface */
	private $propertyInfoExtractor;

	/** @var ModelBuilderInterface */
	private $modelBuilder;

	public function __construct(
		PropertyInfoExtractorInterface $propertyInfoExtractor,
		ModelBuilderInterface $modelBuilder
	) {
		$this->propertyInfoExtractor = $propertyInfoExtractor;
		$this->modelBuilder = $modelBuilder;
	}

	public function getType(string $class, string $targetName): ?TypeInterface {
		try {
			if (method_exists($this->propertyInfoExtractor, 'getType')) {
				// Symfony 7.1+ / 8.0+ with TypeInfo component
				$type = $this->propertyInfoExtractor->getType($class, $targetName);
				if ($type) {
					return $this->createTypeFromTypeInfo($type);
				}
			} elseif (method_exists($this->propertyInfoExtractor, 'getTypes')) {
				// @codeCoverageIgnoreStart
				// Symfony 6.4 / 7.0 with legacy PropertyInfo Type
				$types = $this->propertyInfoExtractor->getTypes($class, $targetName);
				if ($types) {
					return $this->createTypeLegacy($types);
				}
				// @codeCoverageIgnoreEnd
			}
		} catch (\Throwable $e) {
		}
		return null;
	}

	/**
	 * Create type from Symfony TypeInfo component (Symfony 7.1+/8.0+)
	 */
	protected function createTypeFromTypeInfo(object $type): ?TypeInterface {
		$typeString = (string) $type;

		// Handle nullable types - unwrap nullable wrapper
		if (method_exists($type, 'isNullable') && $type->isNullable()) {
			// For nullable types like ?int, get the non-null type
			if (method_exists($type, 'getTypes')) {
				foreach ($type->getTypes() as $innerType) {
					$innerString = (string) $innerType;
					if ($innerString !== 'null') {
						return $this->createTypeFromTypeInfo($innerType);
					}
				}
			}
		}

		// Handle collection/list/array types
		if (method_exists($type, 'getCollectionValueType')) {
			try {
				$valueType = $type->getCollectionValueType();
				return new ArrayType(
					$valueType ? $this->createTypeFromTypeInfo($valueType) : null
				);
			} catch (\Throwable $e) {
				return new ArrayType(null);
			}
		}

		if ($typeString === 'array' || str_starts_with($typeString, 'list<') || str_starts_with($typeString, 'array<')) {
			return new ArrayType(null);
		}

		// Map native types
		$nativeMap = [
			'int' => 'integer',
			'bool' => 'boolean',
			'float' => 'number',
			'double' => 'number',
			'string' => 'string',
		];
		if (isset($nativeMap[$typeString])) {
			return new NativeType($nativeMap[$typeString]);
		}
		if (in_array($typeString, ['integer', 'boolean', 'number', 'string'])) {
			return new NativeType($typeString);
		}

		// Handle object types
		if (class_exists($typeString) || interface_exists($typeString)) {
			if ($typeString === \DateTime::class || is_subclass_of($typeString, \DateTime::class)) {
				return new DateTimeType();
			}
			return $this->modelBuilder->getModel($typeString);
		}

		// Try to get class from object type
		if (method_exists($type, 'getClassName')) {
			$class = $type->getClassName();
			if ($class) {
				if ($class === \DateTime::class || is_subclass_of($class, \DateTime::class)) {
					return new DateTimeType();
				}
				return $this->modelBuilder->getModel($class);
			}
		}

		return null;
	}

	/**
	 * Create type from legacy PropertyInfo Type (Symfony 6.4/7.0)
	 * @codeCoverageIgnore
	 */
	protected function createTypeLegacy(array $types): ?TypeInterface {
		foreach ($types as $type) {
			$builtin = $type->getBuiltinType();
			switch ($builtin) {
				case 'int':
					$builtin = 'integer'; break;
				case 'bool':
					$builtin = 'boolean'; break;
				case 'float':
				case 'double':
					$builtin = 'number'; break;
				default: break;
			}
			if (in_array($builtin, [
				'integer',
				'float',
				'number',
				'string',
				'boolean',
			])) {
				return new NativeType($builtin);
			}
			if ($builtin === 'object') {
				$class = $type->getClassName();
				if ($class === \DateTime::class || is_subclass_of($class, \DateTime::class)) {
					return new DateTimeType();
				}
				return $this->modelBuilder->getModel($class);
			}
			if ($type->isCollection()) {
				$valueType = $type->getCollectionValueTypes();
				return new ArrayType(
					$valueType ? $this->createTypeLegacy($valueType) : null
				);
			}
		}
		return null;
	}
}
