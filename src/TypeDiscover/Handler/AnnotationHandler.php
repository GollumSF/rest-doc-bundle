<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Handler;

use Doctrine\Common\Annotations\Reader;
use GollumSF\RestDocBundle\Annotation\ApiProperty;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;

class AnnotationHandler extends AbstractDecoratorHandler {
	
	/** @var Reader */
	private $reader;

	public function __construct(
		Reader $reader,
		ModelBuilderInterface $modelBuilder
	) {
		parent::__construct($modelBuilder);
		$this->reader = $reader;
	}
	
	protected function getPropertyDecorator(\ReflectionProperty $rProperty): ?ApiProperty {
		return $this->reader->getPropertyAnnotation($rProperty, ApiProperty::class);
	}
	
	protected function getMethodDecorator(\ReflectionMethod $rMethod): ?ApiProperty {
		return $this->reader->getMethodAnnotation($rMethod, ApiProperty::class);
	}
}
