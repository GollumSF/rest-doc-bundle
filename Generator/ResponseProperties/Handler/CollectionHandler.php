<?php
namespace GollumSF\RestDocBundle\Generator\ResponseProperties\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\ResponseProperties\ResponsePropertyCollection;

class CollectionHandler implements HandlerInterface {

	public function generateResponseProperties(ResponsePropertyCollection $responsePropertyCollection, Metadata $metadata, string $method): void {
		if ($metadata->isCollection()) {

			$subProperties = $responsePropertyCollection->toArray();
			$responsePropertyCollection->clear();
			
			$responsePropertyCollection
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
}