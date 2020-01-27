<?php

namespace GollumSF\RestDocBundle\Builder\ModelBuilder\Decorator;

use GollumSF\RestDocBundle\TypeDiscover\Models\ObjectType;

interface DecoratorInterface
{
	public function decorateModel(ObjectType $model): ObjectType;
}