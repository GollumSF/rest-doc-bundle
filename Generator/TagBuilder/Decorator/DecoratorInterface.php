<?php

namespace GollumSF\RestDocBundle\Generator\TagBuilder\Decorator;

use GollumSF\RestDocBundle\Generator\TagBuilder\Tag;

interface DecoratorInterface
{
	public function decorateTag(Tag $tag): Tag;
}