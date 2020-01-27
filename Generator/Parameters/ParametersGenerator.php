<?php
namespace GollumSF\RestDocBundle\Generator\Parameters;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\Parameters\Handler\HandlerInterface;

class ParametersGenerator implements ParametersGeneratorInterface {

	/** @var HandlerInterface[] */
	private $handlers = [];

	public function addHandler(HandlerInterface $handler): void {
		$this->handlers[] = $handler;
	}
	
	public function generate(string $url, Metadata $metadata, string $method): ParameterCollection {
		$parameterCollection = new ParameterCollection();
		foreach ($this->handlers as $handler) {
			$handler->generateParameter($parameterCollection, $url, $metadata, $method);
		}
		return $parameterCollection;
	}
}