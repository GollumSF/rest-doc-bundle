<?php
namespace GollumSF\RestDocBundle\Generator\Parameters\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\Parameters\ParameterCollection;

class RequestPropertiesHandler implements HandlerInterface {
	
	public function generateParameter(ParameterCollection $parameterCollection, string $url, Metadata $metadata, string $method): void {
		foreach ($metadata->getRequestProperties() as $name => $parameter) {
			$parameterCollection->add(
				array_merge(['name' => $name], $parameter)
			);
		}
	}
}