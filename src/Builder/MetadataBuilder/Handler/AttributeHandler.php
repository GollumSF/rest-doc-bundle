<?php

namespace GollumSF\RestDocBundle\Builder\MetadataBuilder\Handler;

use GollumSF\RestDocBundle\Annotation\ApiDescribe;

/**
 * @codeCoverageIgnore 
 */
class AttributeHandler extends AbstractDecoratorHandler
{
	protected function getClassDecorator(\ReflectionClass $rClass): ?ApiDescribe {
		$describeClasses = $rClass->getAttributes(ApiDescribe::class);
		return $describeClasses ? $describeClasses[0]->newInstance() : null;
	}
	
	protected function getMethodDecorator(\ReflectionMethod $rMethod): ?ApiDescribe {
		$describeMethods = $rMethod->getAttributes(ApiDescribe::class);
		return $describeMethods ? $describeMethods[0]->newInstance() : null;
	}
	
}
