<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Handler;

use Doctrine\Common\Annotations\Reader;
use GollumSF\RestDocBundle\Annotation\ApiProperty;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\TypeDiscover\Models\ArrayType;
use GollumSF\RestDocBundle\TypeDiscover\Models\DateTimeType;
use GollumSF\RestDocBundle\TypeDiscover\Models\NativeType;
use GollumSF\RestDocBundle\TypeDiscover\Models\TypeInterface;

class AnnotationHandler implements HandlerInterface
{
	/** @var Reader */
	private $reader;

	/** @var ModelBuilderInterface */
	private $modelBuilder;
	
	public function __construct(
		Reader $reader,
		ModelBuilderInterface $modelBuilder
	) {
		$this->reader = $reader;
		$this->modelBuilder = $modelBuilder;
	}

	public function getType(string $class, string $targetName): ?TypeInterface {
		
		$rClass = new \ReflectionClass($class);
		$type = null;
		
		if ($rClass->hasProperty($targetName)) {
			$rProperty = $rClass->getProperty($targetName);
			/** @var ApiProperty $annotation */
			$annotation = $this->reader->getPropertyAnnotation($rProperty, ApiProperty::class);
			if ($annotation && $annotation->type) {
				$type = $this->createType($annotation->type);
			}
			if ($type && $annotation->collection) {
				$type = new ArrayType($type);
			}
		}

		if ($rClass->hasMethod($targetName)) {
			$rMethod = $rClass->getMethod($targetName);
			/** @var ApiProperty $annotation */
			$annotation = $this->reader->getMethodAnnotation($rMethod, ApiProperty::class);
			if ($annotation && $annotation->type) {
				$type = $this->createType($annotation->type);
			}

			if ($type && $annotation->collection) {
				$type = new ArrayType($type);
			}
		}
		
		return $type;
	}
	
	protected function createType(string $type): ?TypeInterface {
		if ($type === 'datetime') {
			return new DateTimeType();
		} else
		if (class_exists($type)) {
			return $this->modelBuilder->getModel($type);
		}
		return new NativeType($type);
	}
}