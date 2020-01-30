<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Handler;

use Doctrine\Common\Persistence\ManagerRegistry;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\TypeDiscover\Models\ArrayType;
use GollumSF\RestDocBundle\TypeDiscover\Models\DateTimeType;
use GollumSF\RestDocBundle\TypeDiscover\Models\NativeType;
use GollumSF\RestDocBundle\TypeDiscover\Models\RefType;
use GollumSF\RestDocBundle\TypeDiscover\Models\TypeInterface;

class DoctrineHandler implements HandlerInterface {
	
	/** @var ManagerRegistry */
	private $doctrine;
	
	/** @var ModelBuilderInterface */
	private $modelBuilder;
	
	public function __construct(
		ManagerRegistry $doctrine,
		ModelBuilderInterface $modelBuilder
	) {
		$this->doctrine = $doctrine;
		$this->modelBuilder = $modelBuilder;
	}

	public function getType(string $class, string $targetName): ?TypeInterface {

		try {
			$manager = $this->doctrine->getManagerForClass($class);
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