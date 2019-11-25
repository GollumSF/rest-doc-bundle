<?php

namespace GollumSF\RestDocBundle\Generator\MetadataBuilder;

use GollumSF\RestDocBundle\Generator\MetadataBuilder\Handler\HandlerInterface;

class MetadataBuilder implements MetadataBuilderInterface {

	/** @var HandlerInterface[] */
	private $handlers = [];

	/** @var Metadata[] */
	private $cacheMetadataCollection = null;

	public function addHandler(HandlerInterface $handler)
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