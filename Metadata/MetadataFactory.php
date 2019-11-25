<?php

namespace GollumSF\RestDocBundle\Metadata;

use GollumSF\RestDocBundle\Metadata\Handler\HandlerInterface;

class MetadataFactory implements MetadataFactoryInterface {

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