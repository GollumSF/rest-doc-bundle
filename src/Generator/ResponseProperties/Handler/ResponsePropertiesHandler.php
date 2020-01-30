<?php
namespace GollumSF\RestDocBundle\Generator\ResponseProperties\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\Generator\ResponseProperties\ResponsePropertyCollection;

class ResponsePropertiesHandler implements HandlerInterface {
	
	public function generateResponseProperties(ResponsePropertyCollection $responsePropertyCollection, Metadata $metadata, string $method): void {
		if ($metadata->getResponseBodyProperties()) {
			foreach ($metadata->getResponseBodyProperties() as $name => $properties) {
				$responsePropertyCollection->add($name, $properties);
			}
		}
	}
}