<?php

namespace GollumSF\RestDocBundle\Generator\ModelBuilder;

use GollumSF\RestDocBundle\Generator\ModelBuilder\Decorator\DecoratorInterface;

interface ModelBuilderInterface {

	const DECORATOR_TAG = 'gollumsf.rest_doc.model_builder.decorator';

	public function addDecorator(DecoratorInterface $decorator);

	public function getModel(string $lass): Model;

	/**
	 * @return Model[]
	 */
	public function getAllModels(): array;
}