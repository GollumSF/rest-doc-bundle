<?php
namespace GollumSF\RestDocBundle\Generator\Parameters\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\Parameters\ParameterCollection;

interface HandlerInterface {
	public function generateParameter(ParameterCollection $parameterCollection, string $url, Metadata $metadata, string $method): void;
}