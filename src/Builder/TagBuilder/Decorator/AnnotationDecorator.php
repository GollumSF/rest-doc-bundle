<?php

namespace GollumSF\RestDocBundle\Builder\TagBuilder\Decorator;

use Doctrine\Common\Annotations\Reader;
use GollumSF\RestDocBundle\Annotation\ApiEntity;
use GollumSF\RestDocBundle\Builder\TagBuilder\Tag;

class AnnotationDecorator implements DecoratorInterface
{
	/** @var Reader */
	private $reader;
	
	public function __construct(
		Reader $reader
	) {
		$this->reader = $reader;
	}
	
	public function decorateTag(Tag $tag): Tag {

		$rClass = new \ReflectionClass($tag->getClass());
		
		/** @var ApiEntity $apiEntityAnnotation */
		$apiEntityAnnotation = $this->reader->getClassAnnotation($rClass, ApiEntity::class);
		
		if ($apiEntityAnnotation) {
			if ($apiEntityAnnotation->getDescription()) {
				$tag->setDescription($apiEntityAnnotation->getDescription());
			}
			if ($apiEntityAnnotation->getUrl()) {
				$tag->setUrl($apiEntityAnnotation->getUrl());
			}
			if ($apiEntityAnnotation->getDocDescription()) {
				$tag->setDocDescription($apiEntityAnnotation->getDocDescription());
			}
		}
		
		return $tag;
	}
}