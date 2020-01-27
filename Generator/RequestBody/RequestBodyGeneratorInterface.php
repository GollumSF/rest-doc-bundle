<?php
namespace GollumSF\RestDocBundle\Generator\RequestBody;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\RequestBody\Handler\HandlerInterface;

interface RequestBodyGeneratorInterface {
	
	const HANDLER_TAG = 'gollumsf.rest_doc.generator.request_body.handler';

	public function addHandler(HandlerInterface $handler): void;
	
	public function hasRequestBody(Metadata $metadata, string $method): bool;
	
	public function generateProperties(Metadata $metadata, string $method): RequestBodyPropertyCollection;
}