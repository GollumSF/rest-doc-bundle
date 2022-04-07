<?php

namespace GollumSF\RestDocBundle\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class ApiDescribe {

	const ALIAS_NAME = 'gsf_api_describe';

	/** @var string */
	private $entity;

	/** @var bool */
	private $collection = false;

	/** @var string[] */
	private $serializeGroups = [];

	/** @var string[] */
	private $unserializeGroups = [];

	/** @var array */
	private $request = [];

	/** @var array */
	private $response = [];

	/** @var string */
	private $summary = null;

	/**
	 * @param string $entity
	 * @param boolean $collection
	 * @param string|string[] $serializeGroups
	 * @param string|string[]$unserializeGroups
	 * @param array $request
	 * @param array $response
	 * @param string $summary
	 */
	public function __construct(
		$entity = null,
		$collection = null,
		$serializeGroups = [],
		$unserializeGroups = [],
		$request = [],
		$response = [],
		$summary = null
	) {
		if (is_array($entity)) {
			if (function_exists('trigger_deprecation')) {
				// @codeCoverageIgnoreStart
				trigger_deprecation('gollumsf/rest__doc_bundle', '2.8', 'Use native php attributes for %s', __CLASS__);
				// @codeCoverageIgnoreEnd
			}
			$this->entity = isset($entity['entity']) ? $entity['entity'] : null;
			$this->entity = isset($entity['value']) ? $entity['value'] : $this->entity;
			$this->collection = isset($entity['collection']) ? $entity['collection'] : $collection;
			$this->serializeGroups = isset($entity['serializeGroups']) ? (is_array($entity['serializeGroups']) ? $entity['serializeGroups'] : [ $entity['serializeGroups'] ]) : $serializeGroups;
			$this->unserializeGroups = isset($entity['unserializeGroups']) ? (is_array($entity['unserializeGroups']) ? $entity['unserializeGroups'] : [ $entity['unserializeGroups'] ]) : $unserializeGroups;
			$this->request = isset($entity['request']) ? $entity['request'] : $request;
			$this->response = isset($entity['response']) ? $entity['response'] : $response;
			$this->summary = isset($entity['summary']) ? $entity['summary'] : $summary;

			return;
		}
		$this->entity = $entity;
		$this->collection = $collection;
		$this->serializeGroups = is_array($serializeGroups) ? $serializeGroups : [ $serializeGroups ];
		$this->unserializeGroups = is_array($unserializeGroups) ? $unserializeGroups : [ $unserializeGroups ];
		$this->request = $request;
		$this->response = $response;
		$this->summary = $summary;
	}

	/////////////
	// Getters //
	/////////////

	public function getEntity(): ?string {
		return $this->entity;
	}

	public function isCollection(): ?bool {
		return $this->collection;
	}

	public function getSerializeGroups(): array {
		return $this->serializeGroups;
	}

	public function getUnserializeGroups(): array {
		return $this->unserializeGroups;
	}

	public function getRequest(): array {
		return $this->request;
	}

	public function getResponse(): array {
		return $this->response;
	}

	public function getSummary(): ?string {
		return $this->summary;
	}

}
