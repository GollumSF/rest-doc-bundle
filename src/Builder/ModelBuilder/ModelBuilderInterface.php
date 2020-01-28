<?php

namespace GollumSF\RestDocBundle\Builder\ModelBuilder;

use GollumSF\RestDocBundle\Builder\ModelBuilder\Decorator\DecoratorInterface;
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