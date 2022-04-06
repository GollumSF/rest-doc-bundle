<?php

namespace GollumSF\RestDocBundle\Builder\TagBuilder\Decorator;

use GollumSF\RestDocBundle\Annotation\ApiEntity;

/**
 * @codeCoverageIgnore
 */
class AttributeDecorator extends AbstractDecoratorDecorator {
	
	protected function getClassDecorator(\ReflectionClass $rClass): ?ApiEntity {
		$apiEntities = $rClass->getAttributes(ApiEntity::class);
		return count($apiEntities) ? $apiEntities[0]->newInstance() : null;
	}
}
