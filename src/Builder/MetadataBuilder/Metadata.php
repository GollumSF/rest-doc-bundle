<?php

namespace GollumSF\RestDocBundle\Builder\MetadataBuilder;

use GollumSF\RestBundle\Metadata\Serialize\MetadataSerialize;
use GollumSF\RestBundle\Metadata\Unserialize\MetadataUnserialize;
use Symfony\Component\Routing\Route;

class Metadata
{

	/** @var Route */
	private $route;

	/** @var string */
	private $controller;

	/** @var string */
	private $action;
	
	/** @var string */
	private $entity;

	/** @var bool */
	private $collection;

	/** @var string[] */
	private $serializeGroups;

	/** @var string[] */
	private $unserializeGroups;

	/** @var string[] */
	private $request;

	/** @var string[] */
	private $response;

	/** @var MetadataSerialize */
	private $serialize;

	/** @var MetadataUnserialize */
	private $unserialize;

	/** @var string */
	private $summary = null;

	public function __construct(
		Route $route,
		string $controller,
		string $action, 
		string $entity, 
		bool $collection,
		array $serializeGroups,
		array $unserializeGroups,
		array $request,
		array $response,
		?string $summary,
		?MetadataSerialize $serialize,
		?MetadataUnserialize $unserialize
	) {
		$this->route = $route;
		$this->controller = $controller;
		$this->action = $action;
		$this->entity = $entity;
		$this->collection = $collection;
		$this->serializeGroups = $serializeGroups;
		$this->unserializeGroups = $unserializeGroups;
		$this->request = $request;
		$this->response = $response;
		$this->summary = $summary;
		$this->serialize = $serialize;
		$this->unserialize = $unserialize;
	}

	public function getRoute(): Route {
		return $this->route;
	}

	public function getController(): string {
		return $this->controller;
	}

	public function getAction(): string {
		return $this->action;
	}

	public function getEntity(): string {
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

	public function getSummary(): ?string {
		return $this->summary;
	}

	public function getSerialize(): ?MetadataSerialize {
		return $this->serialize;
	}

	public function getUnserialize(): ?MetadataUnserialize {
		return $this->unserialize;
	}
	
}
