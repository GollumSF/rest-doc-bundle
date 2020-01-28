<?php

namespace GollumSF\RestDocBundle\Builder\MetadataBuilder;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Handler\HandlerInterface;

class MetadataBuilder implements MetadataBuilderInterface {

	/** @var HandlerInterface[] */
	private $handlers = [];

	/** @var Metadata[] */
	private $cacheMetadataCollection = null;

	public function addHandler(HandlerInterface $handler): void
	{
		$this->handlers[] = $handler;
	}

	public function getMetadataCollection(): array {
		if ($this->cacheMetadataCollection === null) {
			$this->cacheMetadataCollection = [];
			foreach ($this->handlers as $handler) {
				$this->cacheMetadataCollection = array_merge($this->cacheMetadataCollection, $handler->getMetadataCollection());
			}
		}
		return $this->cacheMetadataCollection;
	}
	
}