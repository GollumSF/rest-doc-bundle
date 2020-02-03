<?php
namespace GollumSF\RestDocBundle\Generator\RequestBody;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\RequestBody\Handler\HandlerInterface;

class RequestBodyGenerator implements RequestBodyGeneratorInterface {

	/** @var HandlerInterface[] */
	private $handlers = [];

	public function addHandler(HandlerInterface $handler): void {
		$this->handlers[] = $handler;
	}
	
	public function hasRequestBody(Metadata $metadata, string $method): bool {
		foreach ($this->handlers as $handler) {
			if ($handler->hasRequestBody($metadata, $method)) {
				return true;
			}
		}
		return false;
	}

	public function generateProperties(Metadata $metadata, string $method): RequestBodyPropertyCollection {
		$requestBodyPropertyCollection = new RequestBodyPropertyCollection();
		foreach ($this->handlers as $handler) {
			if ($handler->hasRequestBody($metadata, $method)) {
				$handler->generateProperties($requestBodyPropertyCollection, $metadata, $method);
			}
		}
		return $requestBodyPropertyCollection;
	}
}