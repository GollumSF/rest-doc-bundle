<?php

namespace GollumSF\RestDocBundle\Generator\TagBuilder;

use \GollumSF\RestDocBundle\Generator\TagBuilder\Decorator\DecoratorInterface;

interface TagBuilderInterface {
	
	const DECORATOR_TAG = 'gollumsf.rest_doc.tag_builder.decorator';

	public function addDecorator(DecoratorInterface $decorator);

	public function getTag(string $lass): Tag;

	/**
	 * @return Tag[]
	 */
	public function getAllTags(): array;

}