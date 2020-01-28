<?php
namespace GollumSF\RestDocBundle\Generator\ResponseProperties\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\ResponseProperties\ResponsePropertyCollection;

interface HandlerInterface {
	public function generateResponseProperties(ResponsePropertyCollection $responsePropertyCollection, Metadata $metadata, string $method): void;
}