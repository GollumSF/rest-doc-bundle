<?php
namespace GollumSF\RestDocBundle\Generator\Parameters\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\Parameters\ParameterCollection;

class UrlHandler implements HandlerInterface {
	
	public function generateParameter(ParameterCollection $parameterCollection, string $url, Metadata $metadata, string $method): void {
		preg_match_all('/\{([a-zA-Z-9_]+)\}/', $url, $match);
		foreach ($match[1] as $key) {
			$parameterCollection->add([
				'name' => $key,
				'in' => 'path',
			]);
		}
	}
}