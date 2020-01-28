<?php
namespace GollumSF\RestDocBundle\Generator\RequestBody\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\RequestBody\RequestBodyPropertyCollection;

interface HandlerInterface {

	public function hasRequestBody(Metadata $metadata, string $method): bool;
	
	public function generateProperties(RequestBodyPropertyCollection $requestBodyPropertyCollection, Metadata $metadata, string $method): void;
}