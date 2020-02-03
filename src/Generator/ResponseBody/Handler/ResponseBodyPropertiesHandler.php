<?php
namespace GollumSF\RestDocBundle\Generator\ResponseBody\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\ResponseBody\ResponseBodyPropertyCollection;

class ResponseBodyPropertiesHandler implements HandlerInterface {

	public function hasResponseBody(Metadata $metadata, string $method): bool {
		$request = $metadata->getResponse();
		$body = (array_key_exists('body', $request) && is_array($request['body'])) ? $request['body'] : [];
		return array_key_exists('properties', $body) && is_array($body['properties']) && $body['properties'];
	}

	public function generateProperties(ResponseBodyPropertyCollection $responseBodyPropertyCollection, Metadata $metadata, string $method): void {
		foreach ($metadata->getResponse()['body']['properties'] as $name => $parameter) {
			$responseBodyPropertyCollection->add($name, $parameter);
		}
	}
}