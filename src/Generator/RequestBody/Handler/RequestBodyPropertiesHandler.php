<?php
namespace GollumSF\RestDocBundle\Generator\RequestBody\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\RequestBody\RequestBodyPropertyCollection;

class RequestBodyPropertiesHandler implements HandlerInterface {
	
	public function hasRequestBody(Metadata $metadata, string $method): bool {
		$request = $metadata->getRequest();
		$body = (array_key_exists('body', $request) && is_array($request['body'])) ? $request['body'] : [];
		return array_key_exists('properties', $body) && is_array($body['properties']) && $body['properties'];
	}

	public function generateProperties(RequestBodyPropertyCollection $requestBodyPropertyCollection, Metadata $metadata, string $method): void {
		foreach ($metadata->getRequest()['body']['properties'] as $name => $property) {
			$requestBodyPropertyCollection->add($name, $property);
		}
	}
}