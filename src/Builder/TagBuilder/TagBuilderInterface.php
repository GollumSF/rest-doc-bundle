<?php

namespace GollumSF\RestDocBundle\Builder\TagBuilder;

use GollumSF\RestDocBundle\Builder\TagBuilder\Decorator\DecoratorInterface;

interface TagBuilderInterface {
	
	const DECORATOR_TAG = 'gollumsf.rest_doc.tag_builder.decorator';

	public function addDecorator(DecoratorInterface $decorator): void;

	public function getTag(string $lass): Tag;

	/**
	 * @return Tag[]
	 */
	public function getAllTags(): array;

}