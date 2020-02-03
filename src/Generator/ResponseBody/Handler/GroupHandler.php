<?php
namespace GollumSF\RestDocBundle\Generator\ResponseBody\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\Generator\ResponseBody\ResponseBodyPropertyCollection;

class GroupHandler implements HandlerInterface {
	
	/** @var ModelBuilderInterface */
	private $modelbuilder;
	
	public function __construct(ModelBuilderInterface $modelbuilder) {
		$this->modelbuilder = $modelbuilder;
	}

	public function hasResponseBody(Metadata $metadata, string $method): bool {
		return !!$metadata->getSerializeGroups();
	}

	public function generateProperties(ResponseBodyPropertyCollection $responseBodyPropertyCollection, Metadata $metadata, string $method): void {
		$groups = $metadata->getSerializeGroups();

		$entity = $metadata->getEntity();
		$model  = $this->modelbuilder->getModel($entity);
		
		$groups = array_merge([strtolower($method)], $groups);
		$groups = array_unique($groups);

		foreach ($model->getProperties() as $property) {
			if (count(array_intersect($property->getGroups(), $groups))) {
				$responseBodyPropertyCollection->add($property->getSerializeName(), $property->getType()->toJson($groups));
			}
		}
	}
}