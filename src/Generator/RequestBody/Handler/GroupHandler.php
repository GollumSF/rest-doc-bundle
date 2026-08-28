<?php
namespace GollumSF\RestDocBundle\Generator\RequestBody\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\Generator\RequestBody\RequestBodyPropertyCollection;

class GroupHandler implements HandlerInterface {

	/** @var ModelBuilderInterface */
	private $modelbuilder;

	public function __construct(ModelBuilderInterface $modelbuilder) {
		$this->modelbuilder = $modelbuilder;
	}

	public function hasRequestBody(Metadata $metadata, string $method): bool {
		return !!$metadata->getUnserializeGroups();
	}

	public function generateProperties(RequestBodyPropertyCollection $requestBodyPropertyCollection, Metadata $metadata, string $method): void {
		$model  = $this->modelbuilder->getModel($this->getUnserializeType($metadata));

		$groups = array_merge([strtolower($method)], $metadata->getUnserializeGroups());
		$groups = array_unique($groups);

		foreach ($model->getPropertiesJson($groups) as $name => $json) {
			$requestBodyPropertyCollection->add($name, $json);
		}
	}

	/**
	 * The request body describes what the action actually unserializes: the type of the
	 * targeted controller parameter. Only falls back on the described entity when the
	 * RestBundle could not resolve one.
	 */
	private function getUnserializeType(Metadata $metadata): string {
		$unserialize = $metadata->getUnserialize();
		if ($unserialize && $unserialize->getType()) {
			return $unserialize->getType();
		}
		return $metadata->getEntity();
	}
}