<?php

namespace GollumSF\RestDocBundle\Reflection;

use Symfony\Component\Routing\Route;

class ControllerAction
{
	/** @var Route*/
	private $route;

	/** @var string */
	private $controllerClass;

	/** @var string */
	private $action;
	
	public function __construct(
		Route $route,
		string $controllerClass,
		string $action
	) {
		$this->route = $route;
		$this->controllerClass = $controllerClass;
		$this->action = $action;
	}

	public function getRoute(): Route {
		return $this->route;
	}
	
	public function getControllerClass(): string {
		return $this->controllerClass;
	}

	public function getAction(): string {
		return $this->action;
	}
}