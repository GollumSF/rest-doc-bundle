<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Handler;

use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\TypeDiscover\Models\ArrayType;
use GollumSF\RestDocBundle\TypeDiscover\Models\DateTimeType;
use GollumSF\RestDocBundle\TypeDiscover\Models\NativeType;
use GollumSF\RestDocBundle\TypeDiscover\Models\TypeInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

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
			$types = $this->propertyInfoExtractor->getTypes($class, $targetName);
			if ($types) {
				return $this->createType($types);
			}
		} catch (\Throwable $e) {
		}
		return null;
	}

	/**
	 * @param Type[] $types
	 */
	protected function createType(array $types): ?TypeInterface {
		
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
				$valueType = version_compare(Kernel::VERSION, '5.3.0', '<') ? [ $type->getCollectionValueType() ] : $type->getCollectionValueTypes();
				return new ArrayType(
					$valueType ? $this->createType($valueType) : null
				);
			}
		}
		return null;
	}
}
