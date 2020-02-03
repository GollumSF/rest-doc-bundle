<?php

namespace GollumSF\RestDocBundle\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class ApiDescribe extends ConfigurationAnnotation {
	
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
	public $response = [];

	/////////////
	// Getters //
	/////////////

	public function getEntity(): ?string {
		return $this->entity;
	}

	public function isCollection(): bool {
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

	public function getAliasName() {
		return self::ALIAS_NAME;
	}

	public function allowArray() {
		return true;
	}

	/////////////
	// Setters //
	/////////////

	public function setEntity(?string $entity): self {
		$this->entity = $entity;
		return $this;
	}

	public function setCollection(bool $collection): self {
		$this->collection = $collection;
		return $this;
	}

	public function setSerializeGroups($serializeGroups): self {
		if (!is_array($serializeGroups)) {
			$serializeGroups = [$serializeGroups];
		}
		$this->serializeGroups = $serializeGroups;
		return $this;
	}

	public function setUnserializeGroups($unserializeGroups): self {
		if (!is_array($unserializeGroups)) {
			$unserializeGroups = [$unserializeGroups];
		}
		$this->unserializeGroups = $unserializeGroups;
		return $this;
	}

	public function setRequest(array $request): self {
		$this->request = $request;
		return $this;
	}

	public function setResponse(array $response): self {
		$this->response = $response;
		return $this;
	}

	public function setValue(?string $entity): self {
		$this->entity = $entity;
		return $this;
	}
}