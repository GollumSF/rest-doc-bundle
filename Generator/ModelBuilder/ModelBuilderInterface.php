<?php

namespace GollumSF\RestDocBundle\Generator\ModelBuilder;

use GollumSF\RestDocBundle\Generator\ModelBuilder\Decorator\DecoratorInterface;
use GollumSF\RestDocBundle\TypeDiscover\Models\ObjectType;

interface ModelBuilderInterface {

	const DECORATOR_TAG = 'gollumsf.rest_doc.model_builder.decorator';

	public function addDecorator(DecoratorInterface $decorator): void;

	public function getModel(string $lass): ObjectType;

	/**
	 * @return ObjectType[]
	 */
	public function getAllModels(): array;
}