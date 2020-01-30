<?php
namespace GollumSF\RestDocBundle\Generator\ResponseProperties\Handler;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\Generator\ResponseProperties\ResponsePropertyCollection;

class GroupHandler implements HandlerInterface {
	
	/** @var ModelBuilderInterface */
	private $modelbuilder;
	
	public function __construct(ModelBuilderInterface $modelbuilder) {
		$this->modelbuilder = $modelbuilder;
	}

	public function generateResponseProperties(ResponsePropertyCollection $responsePropertyCollection, Metadata $metadata, string $method): void {
		if ($groups = $metadata->getSerializeGroups()) {

			$entity = $metadata->getEntity();
			$model  = $this->modelbuilder->getModel($entity);
			
			$groups = array_merge([strtolower($method)], $groups);
			$groups = array_unique($groups);

			foreach ($model->getProperties() as $property) {
				if (count(array_intersect($property->getGroups(), $groups))) {
					$responsePropertyCollection->add($property->getSerializeName(), $property->getType()->toJson($groups));
				}
			}
		}
		
	}
}