<?php
namespace GollumSF\RestDocBundle\Generator\ResponseBody\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\ResponseBody\ResponseBodyPropertyCollection;

interface HandlerInterface {

	public function hasResponseBody(Metadata $metadata, string $method): bool;
	
	public function generateProperties(ResponseBodyPropertyCollection $responseBodyPropertyCollection, Metadata $metadata, string $method): void;
}