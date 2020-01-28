<?php
namespace GollumSF\RestDocBundle\Generator\Parameters\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\Parameters\ParameterCollection;

class CollectionHandler implements HandlerInterface {
	
	public function generateParameter(ParameterCollection $parameterCollection, string $url, Metadata $metadata, string $method): void {
		if ($metadata->isCollection()) {
			$parameterCollection
				->add([
					'name' => 'limit',
					'in' => 'query',
					'required' => false,
					'type' => 'integer',
					'minimum' => 1,
				])
				->add([
					'name' => 'page',
					'in' => 'query',
					'required' => false,
					'type' => 'integer',
				])
				->add([
					'name' => 'order',
					'in' => 'query',
					'required' => false,
					'type' => 'string',
				])
				->add([
					'name' => 'direction',
					'in' => 'query',
					'required' => false,
					'type' => 'string',
					'enum' => [
						"asc",
						"desc",
					]
				])
			;
		}
	}
}