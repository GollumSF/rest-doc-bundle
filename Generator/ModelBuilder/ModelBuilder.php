<?php

namespace GollumSF\RestDocBundle\Generator\ModelBuilder;

use GollumSF\RestDocBundle\Generator\ModelBuilder\Decorator\DecoratorInterface;

class ModelBuilder implements ModelBuilderInterface {

	/** @var DecoratorInterface[] */
	private $decorators = [];
	
	/** @var Model[] */
	private $models = [];

	public function addDecorator(DecoratorInterface $decorator): void {
		$this->decorators[] = $decorator;
	}
	
	public function getModel(string $class): Model
	{
		if (!isset($this->models[$class])) {
			$model = new Model($class);
			foreach ($this->decorators as $decorator) {
				$model = $decorator->decorateModel($model);
			}
			$this->models[$class] = $model;
		}
		return $this->models[$class];
	}

	/**
	 * @return Model[]
	 */
	public function getAllModels(): array {
		return $this->models;
	}
	
}