<?php
namespace GollumSF\RestDocBundle\Generator\ResponseProperties;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\ResponseProperties\Handler\HandlerInterface;

interface ResponsePropertiesGeneratorInterface {
	
	const HANDLER_TAG = 'gollumsf.rest_doc.generator.response_properties.handler';

	public function addHandler(HandlerInterface $handler): void;
	
	public function generate(Metadata $metadata, string $method): ResponsePropertyCollection;
}