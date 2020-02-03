<?php
namespace GollumSF\RestDocBundle\Generator\ResponseBody;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\ResponseBody\Handler\HandlerInterface;

class ResponseBodyGenerator implements ResponseBodyGeneratorInterface {

	/** @var HandlerInterface[] */
	private $handlers = [];

	public function addHandler(HandlerInterface $handler): void {
		$this->handlers[] = $handler;
	}

	public function hasResponseBody(Metadata $metadata, string $method): bool {
		foreach ($this->handlers as $handler) {
			if ($handler->hasResponseBody($metadata, $method)) {
				return true;
			}
		}
		return false;
	}
	
	public function generateProperties(Metadata $metadata, string $method): ResponseBodyPropertyCollection {
		$responsePropertyCollection = new ResponseBodyPropertyCollection();
		foreach ($this->handlers as $handler) {
			if ($handler->hasResponseBody($metadata, $method)) {
				$handler->generateProperties($responsePropertyCollection, $metadata, $method);
			}
		}
		return $responsePropertyCollection;
	}
}