<?php

namespace GollumSF\RestDocBundle\Builder\MetadataBuilder\Handler;

use Doctrine\Common\Annotations\Reader;
use GollumSF\ControllerActionExtractorBundle\Extractor\ControllerAction;
use GollumSF\ControllerActionExtractorBundle\Extractor\ControllerActionExtractorInterface;
use GollumSF\RestBundle\Annotation\Serialize;
use GollumSF\RestBundle\Annotation\Unserialize;
use GollumSF\RestDocBundle\Annotation\ApiDescribe;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
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

		$metadataCollection = [];
		
		foreach ($this->router->getRouteCollection() as $routeName => $route) {
			$controllerAction = $this->controllerActionExtractor->extractFromRoute($route);
			if ($controllerAction) {
				$metadata = $this->createMetadata($route, $controllerAction);
				if ($metadata) {
					$metadataCollection[] = $metadata;
				}
			}
		}
		
		return $metadataCollection;
	}

	protected function createMetadata(Route $route, ControllerAction $controllerAction): ?Metadata {

		$controller = $controllerAction->getControllerClass();
		$action = $controllerAction->getActionMethod();
		
		$rClass = new \ReflectionClass($controller);
		$rMethod = $rClass->getMethod($action);

		/** @var ApiDescribe $describeClass */
		/** @var ApiDescribe $describeMethod */
		$describeClass = $this->reader->getClassAnnotation($rClass, ApiDescribe::class);
		$describeMethod = $this->reader->getMethodAnnotation($rMethod, ApiDescribe::class);

		$entity = null;
		$isCollection = null;

		if ($describeMethod) {
			$entity = $describeMethod->getEntity();
			$isCollection = $describeMethod->isCollection();
		}
		if ($describeClass) {
			if ($entity === null) {
				$entity = $describeClass->getEntity();
			}
			if ($isCollection === null) {
				$isCollection = $describeClass->isCollection();
			}
		}
		
		if ($entity) {

			/** @var Serialize $annoSerialize */
			/** @var Unserialize $annoUnserialize */
			$annoSerialize = $this->reader->getMethodAnnotation($rMethod, Serialize::class);
			$annoUnserialize = $this->reader->getMethodAnnotation($rMethod, Unserialize::class);

			$serializeGroups   = $annoSerialize   ? $annoSerialize->getGroups()   : [];
			$unserializeGroups = $annoUnserialize ? $annoUnserialize->getGroups() : [];
			
			if ($describeClass  && $describeClass ->getSerializeGroups()) $serializeGroups = array_merge($serializeGroups, $describeClass ->getSerializeGroups());
			if ($describeMethod && $describeMethod->getSerializeGroups()) $serializeGroups = array_merge($serializeGroups, $describeMethod->getSerializeGroups());

			if ($describeClass  && $describeClass ->getUnserializeGroups()) $unserializeGroups = array_merge($unserializeGroups, $describeClass ->getUnserializeGroups());
			if ($describeMethod && $describeMethod->getUnserializeGroups()) $unserializeGroups = array_merge($unserializeGroups, $describeMethod->getUnserializeGroups());

			$serializeGroups   = array_unique($serializeGroups);
			$unserializeGroups = array_unique($unserializeGroups);

			$request = [];
			if ($describeClass  && $describeClass->getRequest())  $request = array_merge($request, $describeClass->getRequest());
			if ($describeMethod && $describeMethod->getRequest()) $request = array_merge($request, $describeMethod->getRequest());
			

			$response = [];
			if ($describeClass  && $describeClass->getResponse())  $response = array_merge($response, $describeClass->getResponse());
			if ($describeMethod && $describeMethod->getResponse()) $response = array_merge($response, $describeMethod->getResponse());
				
			
			return new Metadata(
				$route,
				$controller,
				$action,
				$entity,
				!!$isCollection,
				$serializeGroups,
				$unserializeGroups,
				$request,
				$response,
				$annoSerialize,
				$annoUnserialize
			);
		}

		return null;
	}
}