<?php

namespace GollumSF\RestDocBundle\Builder\MetadataBuilder\Handler;

use  GollumSF\ControllerActionExtractorBundle\Extractor\ControllerAction;
use GollumSF\ControllerActionExtractorBundle\Extractor\ControllerActionExtractorInterface;
use GollumSF\RestBundle\Annotation\Serialize;
use GollumSF\RestBundle\Annotation\Unserialize;
use GollumSF\RestBundle\Metadata\Serialize\MetadataSerializeManagerInterface;
use GollumSF\RestBundle\Metadata\Unserialize\MetadataUnserializeManagerInterface;
use GollumSF\RestDocBundle\Annotation\ApiDescribe;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractDecoratorHandler implements HandlerInterface
{

	/** @var RouterInterface */
	private $router;

	/** @var ControllerActionExtractorInterface */
	private $controllerActionExtractor;
	
	/** @var MetadataSerializeManagerInterface */
	private $metadataSerializeManager;
	
	/** @var MetadataUnserializeManagerInterface */
	private $metadataUnserializeManager;
	
	public function __construct(
		RouterInterface $router,
		ControllerActionExtractorInterface $controllerActionExtractor,
		MetadataSerializeManagerInterface $metadataSerializeManager,
		MetadataUnserializeManagerInterface $metadataUnserializeManager
	) {
		$this->router = $router;
		$this->controllerActionExtractor = $controllerActionExtractor;
		$this->metadataSerializeManager = $metadataSerializeManager;
		$this->metadataUnserializeManager = $metadataUnserializeManager;
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
	
	/**
	 * @param \ReflectionClass $rClass
	 * @return ?ApiDescribe
	 */
	protected abstract function getClassDecorator(\ReflectionClass $rClass): ?ApiDescribe;
	
	/**
	 * @param \ReflectionMethod $rMethod
	 * @return ?ApiDescribe
	 */
	protected abstract function getMethodDecorator(\ReflectionMethod $rMethod): ?ApiDescribe;
	
	protected function createMetadata(Route $route, ControllerAction $controllerAction): ?Metadata {

		$controller = $controllerAction->getControllerClass();
		$action = $controllerAction->getActionMethod();
		
		$rClass = new \ReflectionClass($controller);
		$rMethod = $rClass->getMethod($action);

		/** @var ApiDescribe $describeMethod */
		$describeClass = $this->getClassDecorator($rClass);
		$describeMethod = $this->getMethodDecorator($rMethod);
		
		$entity = null;
		$isCollection = null;
		$summary = null; 
		
		if ($describeMethod) {
			$entity = $describeMethod->getEntity();
			$isCollection = $describeMethod->isCollection();
			$summary = $describeMethod->getSummary();
		}
		if ($describeClass) {
			if ($entity === null) {
				$entity = $describeClass->getEntity();
			}
			if ($isCollection === null) {
				$isCollection = $describeClass->isCollection();
			}
			if ($summary === null) {
				$summary = $describeClass->getSummary();
			}
		}
		
		if ($entity) {

			$metadataSerialize = $this->metadataSerializeManager->getMetadata($controller, $action);
			$metadataUnserialize = $this->metadataUnserializeManager->getMetadata($controller, $action);

			$serializeGroups   = $metadataSerialize   ? $metadataSerialize->getGroups()   : [];
			$unserializeGroups = $metadataUnserialize ? $metadataUnserialize->getGroups() : [];
			
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
				$summary,
				$metadataSerialize,
				$metadataUnserialize
			);
		}

		return null;
	}
}
