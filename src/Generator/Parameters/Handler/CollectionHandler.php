<?php
namespace GollumSF\RestDocBundle\Generator\Parameters\Handler;

use GollumSF\RestBundle\Metadata\Sort\MetadataSortManagerInterface;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\Parameters\ParameterCollection;

class CollectionHandler implements HandlerInterface {

	/** @var MetadataSortManagerInterface */
	private $metadataSortManager;

	public function __construct(MetadataSortManagerInterface $metadataSortManager) {
		$this->metadataSortManager = $metadataSortManager;
	}

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
				->add($this->createOrderParameter($metadata))
			;
		}
	}

	/**
	 * Sort keys are comma separated, each optionally suffixed by `:asc` or `:desc`.
	 * When the entity declares sortables, they are the only accepted keys.
	 */
	private function createOrderParameter(Metadata $metadata): array {

		$parameter = [
			'name' => 'order',
			'in' => 'query',
			'required' => false,
			'type' => 'string',
		];

		$keys = $this->metadataSortManager
			->getMetadata($metadata->getEntity(), $metadata->getController(), $metadata->getAction())
			->getKeys()
		;

		if ($keys) {
			$parameter['description'] = sprintf(
				'Comma separated sort keys, each optionally suffixed by ":asc" or ":desc". Allowed keys: %s.',
				implode(', ', $keys)
			);
			$parameter['example'] = $keys[0].':desc';
		} else {
			$parameter['description'] = 'Comma separated sort keys, each optionally suffixed by ":asc" or ":desc".';
		}

		return $parameter;
	}
}
