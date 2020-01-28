<?php
namespace GollumSF\RestDocBundle\Generator\ResponseProperties;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\ResponseProperties\Handler\HandlerInterface;

class ResponsePropertiesGenerator implements ResponsePropertiesGeneratorInterface {

	/** @var HandlerInterface[] */
	private $handlers = [];

	public function addHandler(HandlerInterface $handler): void {
		$this->handlers[] = $handler;
	}
	
	public function generate(Metadata $metadata, string $method): ResponsePropertyCollection {
		$responsePropertyCollection = new ResponsePropertyCollection();
		foreach ($this->handlers as $handler) {
			$handler->generateResponseProperties($responsePropertyCollection, $metadata, $method);
		}
		return $responsePropertyCollection;
	}
}