<?php
namespace GollumSF\RestDocBundle\Generator\RequestBody\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\RequestBody\RequestBodyPropertyCollection;

class RequestBodyPropertiesHandler implements HandlerInterface {
	
	public function hasRequestBody(Metadata $metadata, string $method): bool {
		return !!$metadata->getRequestBodyProperties();
	}

	public function generateProperties(RequestBodyPropertyCollection $requestBodyPropertyCollection, Metadata $metadata, string $method): void {
		foreach ($metadata->getRequestBodyProperties() as $name => $property) {
			$requestBodyPropertyCollection->add($name, $property);
		}
	}
}