<?php

namespace GollumSF\RestDocBundle\Builder\TagBuilder\Decorator;

use Doctrine\Common\Annotations\Reader;
use GollumSF\RestDocBundle\Annotation\ApiEntity;

class AnnotationDecorator extends AbstractDecoratorDecorator {
	
	/** @var Reader */
	private $reader;
	
	public function __construct(
		Reader $reader
	) {
		$this->reader = $reader;
	}
	
	protected function getClassDecorator(\ReflectionClass $rClass): ?ApiEntity {
		return $this->reader->getClassAnnotation($rClass, ApiEntity::class);
	}
}
