<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Handler;

use Doctrine\Common\Annotations\Reader;
use GollumSF\RestDocBundle\Annotation\ApiProperty;
use GollumSF\RestDocBundle\TypeDiscover\Models\ArrayType;
use GollumSF\RestDocBundle\TypeDiscover\Models\DateTimeType;
use GollumSF\RestDocBundle\TypeDiscover\Models\NativeType;
use GollumSF\RestDocBundle\TypeDiscover\Models\TypeInterface;

class AnnotationHandler implements HandlerInterface
{
	/** @var Reader */
	private $reader;
	
	public function __construct(Reader $reader) {
		$this->reader = $reader;
	}

	public function getType(string $class, string $targetName): ?TypeInterface {
		
		$rClass = new \ReflectionClass($class);
		$type = null;
		
		if ($rClass->hasProperty($targetName)) {
			$rProperty = $rClass->getProperty($targetName);
			/** @var ApiProperty $annotation */
			$annotation = $this->reader->getPropertyAnnotation($rProperty, ApiProperty::class);
			if ($annotation && $annotation->type) {
				if ($annotation->type === 'datetime') {
					$type = new DateTimeType();
				} else {
					$type = new NativeType($annotation->type);
				}
			}

			if ($type && $annotation->collection) {
				$type = new ArrayType($type);
			}
		}

		if ($rClass->hasMethod($targetName)) {
			$rProperty = $rClass->getMethod($targetName);
			/** @var ApiProperty $annotation */
			$annotation = $this->reader->getMethodAnnotation($rProperty, ApiProperty::class);
			if ($annotation && $annotation->type) {
				if ($annotation->type === 'datetime') {
					$type = new DateTimeType();
				} else {
					$type = new NativeType($annotation->type);
				}
			}

			if ($type && $annotation->collection) {
				$type = new ArrayType($type);
			}
		}
		
		return $type;
	}
}