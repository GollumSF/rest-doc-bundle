<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Handler;

use Doctrine\Persistence\ManagerRegistry;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\TypeDiscover\Models\ArrayType;
use GollumSF\RestDocBundle\TypeDiscover\Models\DateTimeType;
use GollumSF\RestDocBundle\TypeDiscover\Models\NativeType;
use GollumSF\RestDocBundle\TypeDiscover\Models\RefType;
use GollumSF\RestDocBundle\TypeDiscover\Models\TypeInterface;

class DoctrineHandler implements HandlerInterface {
	
	/** @var ManagerRegistry */
	private $managerRegistry;
	
	/** @var ModelBuilderInterface */
	private $modelBuilder;
	
	public function __construct(
		ModelBuilderInterface $modelBuilder
	) {
		$this->modelBuilder = $modelBuilder;
	}

	public function setManagerRegistry(ManagerRegistry $managerRegistry): self {
		$this->managerRegistry = $managerRegistry;
		return $this;
	}

	public function getType(string $class, string $targetName): ?TypeInterface {

		if (!$this->managerRegistry) {
			return null;
		}
		
		try {
			$manager = $this->managerRegistry->getManagerForClass($class);
			if ($manager && !$manager->getMetadataFactory()->isTransient($class)) {
				$metadata = $manager->getClassMetadata($class);
				
				if ($metadata->hasField($targetName)) {
					$type = $metadata->getTypeOfField($targetName);
					if (in_array($type, [
						'integer',
						'float',
						'double',
						'string',
						'boolean',
					])) {
						return new NativeType($type);
					}
					if ($type === 'datetime') {
						return new DateTimeType();
					}
				}
				if ($metadata->hasAssociation($targetName)) {
					$type = $this->modelBuilder->getModel($metadata->getAssociationTargetClass($targetName)); 
					if ($metadata->isCollectionValuedAssociation($targetName)) {
						return new ArrayType($type);
					}
					return $type;
				}
			}
		} catch (\Throwable $e) {
		}
		
		return null;
	}
}