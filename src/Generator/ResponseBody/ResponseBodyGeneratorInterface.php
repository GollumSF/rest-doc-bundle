<?php
namespace GollumSF\RestDocBundle\Generator\ResponseBody;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\ResponseBody\Handler\HandlerInterface;

interface ResponseBodyGeneratorInterface {
	
	const HANDLER_TAG = 'gollumsf.rest_doc.generator.response_properties.handler';

	public function addHandler(HandlerInterface $handler): void;

	public function hasResponseBody(Metadata $metadata, string $method): bool;
	
	public function generateProperties(Metadata $metadata, string $method): ResponseBodyPropertyCollection;
}