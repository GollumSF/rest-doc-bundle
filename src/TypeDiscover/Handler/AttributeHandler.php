<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Handler;

use GollumSF\RestDocBundle\Annotation\ApiProperty;

/**
 * @codeCoverageIgnore
 */
class AttributeHandler extends AbstractDecoratorHandler {
	
	protected function getPropertyDecorator(\ReflectionProperty $rProperty): ?ApiProperty {
		$apiProperties = $rProperty->getAttributes(ApiProperty::class);
		return count($apiProperties) ? $apiProperties[0]->newInstance() : null;
	}
	
	protected function getMethodDecorator(\ReflectionMethod $rMethod): ?ApiProperty {
		$apiProperties = $rMethod->getAttributes(ApiProperty::class);
		return count($apiProperties) ? $apiProperties[0]->newInstance() : null;
	}
}
