<?php

namespace GollumSF\RestDocBundle\Builder\TagBuilder\Decorator;

use GollumSF\RestDocBundle\Annotation\ApiEntity;
use GollumSF\RestDocBundle\Builder\TagBuilder\Tag;

abstract class AbstractDecoratorDecorator implements DecoratorInterface {
	
	protected abstract function getClassDecorator(\ReflectionClass $rClass): ?ApiEntity;
	
	public function decorateTag(Tag $tag): Tag {
		
		$rClass = new \ReflectionClass($tag->getClass());
		
		$apiEntityAnnotation = $this->getClassDecorator($rClass);
		
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
