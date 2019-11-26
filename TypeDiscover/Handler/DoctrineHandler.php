<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Handler;

use Doctrine\Common\Persistence\ManagerRegistry;
use GollumSF\RestDocBundle\TypeDiscover\Models\NativeType;
use GollumSF\RestDocBundle\TypeDiscover\Models\TypeInterface;

class DoctrineHandler implements HandlerInterface {
	
	/** @var ManagerRegistry */
	private $doctrine;
	
	public function __construct(ManagerRegistry $doctrine) {
		$this->doctrine = $doctrine;
	}

	public function getType(string $class, string $targetName): ?TypeInterface {

		try {
			$manager = $this->doctrine->getManagerForClass($class);
			if ($manager) {
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
						return new NativeType('string');
					}
				}
				
			}
		} catch (\Throwable $e) {
		}
		
		return null;
	}
}