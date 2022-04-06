<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Handler;

use Doctrine\Common\Annotations\Reader;
use GollumSF\RestDocBundle\Annotation\ApiProperty;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\TypeDiscover\Models\ArrayType;
use GollumSF\RestDocBundle\TypeDiscover\Models\DateTimeType;
use GollumSF\RestDocBundle\TypeDiscover\Models\NativeType;
use GollumSF\RestDocBundle\TypeDiscover\Models\TypeInterface;

abstract class AbstractDecoratorHandler implements HandlerInterface
{
	/** @var ModelBuilderInterface */
	private $modelBuilder;
	
	public function __construct(
		ModelBuilderInterface $modelBuilder
	) {
		$this->modelBuilder = $modelBuilder;
	}
	
	protected abstract function getPropertyDecorator(\ReflectionProperty $rProperty): ?ApiProperty;
	protected abstract function getMethodDecorator(\ReflectionMethod $rMethod): ?ApiProperty;
	
	public function getType(string $class, string $targetName): ?TypeInterface {
		
		$rClass = new \ReflectionClass($class);
		$type = null;
		
		if ($rClass->hasProperty($targetName)) {
			$rProperty = $rClass->getProperty($targetName);
			$annotation = $this->getPropertyDecorator($rProperty);
			if ($annotation && $annotation->type) {
				$type = $this->createType($annotation->type);
			}
			if ($type && $annotation->collection) {
				$type = new ArrayType($type);
			}
		}

		if ($rClass->hasMethod($targetName)) {
			$rMethod = $rClass->getMethod($targetName);
			$annotation = $this->getMethodDecorator($rMethod);
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
		if ($type === 'float' || $type === 'double') {
			return new NativeType('number');
		} else
		if ($type === 'datetime') {
			return new DateTimeType();
		} else
		if (class_exists($type)) {
			return $this->modelBuilder->getModel($type);
		}
		return new NativeType($type);
	}
}
