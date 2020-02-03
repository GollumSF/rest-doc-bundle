<?php
namespace GollumSF\RestDocBundle\Generator\ResponseBody\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\ResponseBody\ResponseBodyPropertyCollection;

class CollectionHandler implements HandlerInterface {

	public function hasResponseBody(Metadata $metadata, string $method): bool {
		return $metadata->isCollection();
	}
	
	public function generateProperties(ResponseBodyPropertyCollection $responseBodyPropertyCollection, Metadata $metadata, string $method): void {
		$subProperties = $responseBodyPropertyCollection->toArray();
		$responseBodyPropertyCollection->clear();

		$responseBodyPropertyCollection
			->add('total', [
				'type' => 'integer'
			])
			->add('data', [
				'type' => 'array',
				'items' => [
					'type' => 'object',
					'properties' => $subProperties
				],
			])
		;
	}
}