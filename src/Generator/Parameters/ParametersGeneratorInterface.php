<?php
namespace GollumSF\RestDocBundle\Generator\Parameters;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\Parameters\Handler\HandlerInterface;

interface ParametersGeneratorInterface {
	
	const HANDLER_TAG = 'gollumsf.rest_doc.generator.parameters.handler';

	public function addHandler(HandlerInterface $handler): void;
	
	public function generate(string $url, Metadata $metadata, string $method): ParameterCollection;
}