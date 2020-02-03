<?php
namespace GollumSF\RestDocBundle\Generator\Parameters\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\Parameters\ParameterCollection;

class RequestPropertiesHandler implements HandlerInterface {
	
	public function generateParameter(ParameterCollection $parameterCollection, string $url, Metadata $metadata, string $method): void {
		$request = $metadata->getRequest();
		if (array_key_exists('parameters', $request) && is_array($request['parameters'])) {
			foreach ($request['parameters'] as $name => $parameter) {
				$parameterCollection->add(
					array_merge(['name' => $name], $parameter)
				);
			}
		}
	}
}