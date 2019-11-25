<?php

namespace GollumSF\RestDocBundle\Generator\ModelBuilder\Decorator;

use GollumSF\RestDocBundle\Generator\ModelBuilder\Model;

interface DecoratorInterface
{
	public function decorateModel(Model $model): Model;
}