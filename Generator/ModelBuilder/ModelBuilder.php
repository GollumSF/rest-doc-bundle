<?php

namespace GollumSF\RestDocBundle\Generator\ModelBuilder;

use GollumSF\RestDocBundle\Generator\ModelBuilder\Decorator\DecoratorInterface;
use GollumSF\RestDocBundle\TypeDiscover\Models\ObjectType;

class ModelBuilder implements ModelBuilderInterface {

	/** @var DecoratorInterface[] */
	private $decorators = [];
	
	/** @var ObjectType[] */
	private $models = [];

	public function addDecorator(DecoratorInterface $decorator): void {
		$this->decorators[] = $decorator;
	}
	
	public function getModel(string $class): ObjectType
	{
		if (!isset($this->models[$class])) {
			$model = new ObjectType($class);
			foreach ($this->decorators as $decorator) {
				$model = $decorator->decorateModel($model);
			}
			$this->models[$class] = $model;
		}
		return $this->models[$class];
	}

	/**
	 * @return ObjectType[]
	 */
	public function getAllModels(): array {
		return $this->models;
	}
	
}