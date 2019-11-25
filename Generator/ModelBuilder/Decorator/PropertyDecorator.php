<?php

namespace GollumSF\RestDocBundle\Generator\ModelBuilder\Decorator;

use GollumSF\RestDocBundle\Generator\ModelBuilder\Model;
use GollumSF\RestDocBundle\Generator\ModelBuilder\ModelProperty;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class PropertyDecorator implements DecoratorInterface
{
	/** @var NameConverterInterface */
	private $nameConverter;
	
	/** @var ClassMetadataFactoryInterface */
	private $classMetadataFactory;
	
	public function __construct(
		NameConverterInterface        $nameConverter,
		ClassMetadataFactoryInterface $classMetadataFactory
	) {
		$this->nameConverter = $nameConverter;
		$this->classMetadataFactory = $classMetadataFactory;
	}
	
	public function decorateModel(Model $model): Model {
		$class = $model->getClass();
		
		$metadata = $this->classMetadataFactory->getMetadataFor($class);
		foreach ($metadata->getAttributesMetadata() as $attributesMetadata) {
			
			$name = $attributesMetadata->getName();
			$serializeName = $attributesMetadata->getSerializedName();
			if (!$serializeName) {
				$serializeName = $this->nameConverter->normalize($name);
			}

			$type = 'string';

			$property = new ModelProperty(
				$name,
				$serializeName,
				$type,
				$attributesMetadata->getGroups()
			);
			$model->addProperty($property);
		}
		
		return $model;
	}
}