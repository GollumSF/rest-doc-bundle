<?php

namespace GollumSF\RestDocBundle\Generator\MetadataBuilder\Handler;

use Doctrine\Common\Annotations\Reader;
use GollumSF\RestBundle\Annotation\Serialize;
use GollumSF\RestBundle\Annotation\Unserialize;
use GollumSF\RestDocBundle\Annotation\ApiDescribe;
use GollumSF\RestDocBundle\Generator\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Reflection\ControllerActionExtractorInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class AnnotationHandler implements HandlerInterface
{

	/** @var RouterInterface */
	private $router;

	/** @var Reader */
	private $reader;

	/** @var ControllerActionExtractorInterface */
	private $controllerActionExtractor;

	public function __construct(
		RouterInterface $router,
		Reader $reader,
		ControllerActionExtractorInterface $controllerActionExtractor
	) {
		$this->router = $router;
		$this->reader = $reader;
		$this->controllerActionExtractor = $controllerActionExtractor;
	}

	/**
	 * @return Metadata[]
	 */
	public function getMetadataCollection(): array {

		$etadataCollection = [];

		$controllerActions = [];
		foreach ($this->router->getRouteCollection() as $routeName => $route) {
			$controllerAction = $this->controllerActionExtractor->extractFromRoute($route);
			if ($controllerAction) {
				$controllerActions[] = $controllerAction;
			}
		}

		foreach ($controllerActions as $controllerAction) {

			$route = $controllerAction->getRoute();
			$controller = $controllerAction->getControllerClass();
			$action = $controllerAction->getAction();

			$metadata = $this->createMatadata($route, $controller, $action);
			if ($metadata) {
				$etadataCollection[] = $metadata;
			}
		}
		
		return $etadataCollection;
	}

	protected function createMatadata(Route $route, string $controller, $action): ?Metadata {

		$rClass = new \ReflectionClass($controller);
		$rMethod = $rClass->getMethod($action);

		/** @var ApiDescribe $describeClass */
		/** @var ApiDescribe $describeMethod */
		$describeClass = $this->reader->getClassAnnotation($rClass, ApiDescribe::class);
		$describeMethod = $this->reader->getMethodAnnotation($rMethod, ApiDescribe::class);

		$entity = null;
		$isCollection = null;

		if ($describeMethod) {
			$entity = $describeMethod->entity;
			$isCollection = $describeMethod->collection;
		}
		if ($describeClass) {
			if ($entity === null) {
				$entity = $describeClass->entity;
			}
			if ($isCollection === null) {
				$isCollection = $describeClass->collection;
			}
		}

		if ($entity) {

			/** @var Serialize $annoSerialize */
			/** @var Unserialize $annoUnserialize */
			$annoSerialize = $this->reader->getMethodAnnotation($rMethod, Serialize::class);
			$annoUnserialize = $this->reader->getMethodAnnotation($rMethod, Unserialize::class);

			return new Metadata(
				$route,
				$controller,
				$action,
				$entity,
				!!$isCollection,
				$annoSerialize,
				$annoUnserialize
			);
		}

		return null;
	}
}