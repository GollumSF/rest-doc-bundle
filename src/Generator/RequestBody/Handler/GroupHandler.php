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
		$entity = $metadata->getEntity();
		$model  = $this->modelbuilder->getModel($entity);
		
		$groups = array_merge([strtolower($method)], $metadata->getUnserializeGroups());
		$groups = array_unique($groups);

		foreach ($model->getProperties() as $property) {
			if (count(array_intersect($property->getGroups(), $groups))) {
				$requestBodyPropertyCollection->add($property->getSerializeName(), $property->getType()->toJson($groups));
			}
		}
	}
}