<?php

namespace GollumSF\RestDocBundle\Generator\ModelBuilder\Decorator;

use GollumSF\RestDocBundle\Generator\ModelBuilder\Model;
use GollumSF\RestDocBundle\Generator\ModelBuilder\ModelProperty;
use GollumSF\RestDocBundle\TypeDiscover\TypeDiscoverInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class PropertyDecorator implements DecoratorInterface
{
	/** @var NameConverterInterface */
	private $nameConverter;
	
	/** @var ClassMetadataFactoryInterface */
	private $classMetadataFactory;
	
	/** @var TypeDiscoverInterface */
	private $typeDiscover;
	
	public function __construct(
		NameConverterInterface        $nameConverter,
		ClassMetadataFactoryInterface $classMetadataFactory,
		TypeDiscoverInterface $typeDiscover
	) {
		$this->nameConverter = $nameConverter;
		$this->classMetadataFactory = $classMetadataFactory;
		$this->typeDiscover = $typeDiscover;
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
			
			$type = $this->typeDiscover->getType($class, $name);
			
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