<?php

namespace GollumSF\RestDocBundle\Generator\MetadataBuilder;

use GollumSF\RestBundle\Annotation\Serialize;
use GollumSF\RestBundle\Annotation\Unserialize;
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

	/** @var Serialize */
	private $serialize;

	/** @var Unserialize */
	private $unserialize;

	public function __construct(
		Route $route,
		string $controller,
		string $action, 
		string $entity, 
		bool $collection,
		?Serialize $serialize,
		?Unserialize $unserialize
	) {
		$this->route = $route;
		$this->controller = $controller;
		$this->action = $action;
		$this->entity = $entity;
		$this->collection = $collection;
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

	public function getSerialize(): ?Serialize {
		return $this->serialize;
	}

	public function getUnserialize(): ?Unserialize {
		return $this->unserialize;
	}
}