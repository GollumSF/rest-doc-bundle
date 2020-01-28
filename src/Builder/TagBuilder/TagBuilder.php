<?php

namespace GollumSF\RestDocBundle\Builder\TagBuilder;

use GollumSF\RestDocBundle\Builder\TagBuilder\Decorator\DecoratorInterface;

class TagBuilder implements TagBuilderInterface {
	
	/** @var DecoratorInterface[] */
	private $decorators = [];
	
	/** @var Tag[] */
	private $tags = [];

	public function addDecorator(DecoratorInterface $decorator): void {
		$this->decorators[] = $decorator;
	}
	
	public function getTag(string $class): Tag
	{
		if (!isset($this->tags[$class])) {
			$tag = new Tag($class);
			foreach ($this->decorators as $decorator) {
				$tag = $decorator->decorateTag($tag);
			}
			$this->tags[$class] = $tag;
		}
		return $this->tags[$class];
	}

	/**
	 * @return Tag[]
	 */
	public function getAllTags(): array {
		return $this->tags;
	}
	
}