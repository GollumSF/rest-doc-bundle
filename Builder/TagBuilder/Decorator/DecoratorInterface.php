<?php

namespace GollumSF\RestDocBundle\Builder\TagBuilder\Decorator;

use GollumSF\RestDocBundle\Builder\TagBuilder\Tag;

interface DecoratorInterface
{
	public function decorateTag(Tag $tag): Tag;
}