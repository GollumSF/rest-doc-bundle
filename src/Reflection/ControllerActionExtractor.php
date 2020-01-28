<?php

namespace GollumSF\RestDocBundle\Reflection;

use Dev\Controller\AssetController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;

class ControllerActionExtractor implements ControllerActionExtractorInterface
{
	/** @var ContainerInterface  */
	private $container;
	
	public function __construct(
		ContainerInterface $container
	) {
		$this->container = $container;
	}

	public function extractFromRoute(Route $route): ?ControllerAction {

		$controller = $route->getDefault('_controller');
		if (!$controller) {
			return null;
		}
		if (\is_string($controller)) {
			if (false === strpos($controller, '::')) {
				$controller = [
					$controller,
					'__invoke'
				];
			} else {
				$controller = explode('::', $controller);
			}
		}
		
		if (is_array($controller) && isset($controller[1])) {
			if (is_object($controller[0])) {
				$controller[0] = get_class($controller[0]);
			}
			if ($this->container->has($controller[0])) {
				$controller[0] = get_class($this->container->get($controller[0]));
			}
			if (class_exists($controller[0])) {
				return new ControllerAction(
					$route,
					$controller[0],
					$controller[1]
				);
			}
		}
		
		return null;
	}
}