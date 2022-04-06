<?php

namespace GollumSF\RestDocBundle\Builder\MetadataBuilder\Handler;

use Doctrine\Common\Annotations\Reader;
use GollumSF\ControllerActionExtractorBundle\Extractor\ControllerActionExtractorInterface;
use GollumSF\RestBundle\Metadata\Serialize\MetadataSerializeManagerInterface;
use GollumSF\RestBundle\Metadata\Unserialize\MetadataUnserializeManagerInterface;
use GollumSF\RestDocBundle\Annotation\ApiDescribe;
use Symfony\Component\Routing\RouterInterface;

class AnnotationHandler extends AbstractDecoratorHandler
{

	/** @var Reader */
	private $reader;

	public function __construct(
		RouterInterface $router,
		ControllerActionExtractorInterface $controllerActionExtractor,
		MetadataSerializeManagerInterface $metadataSerializeManager,
		MetadataUnserializeManagerInterface $metadataUnserializeManager,
		Reader $reader
	) {	
		parent::__construct(
			$router,
			$controllerActionExtractor,
			$metadataSerializeManager,
			$metadataUnserializeManager
		);
		$this->reader = $reader;
	}
	
	protected function getClassDecorator(\ReflectionClass $rClass): ?ApiDescribe {
		return $this->reader->getClassAnnotation($rClass, ApiDescribe::class);
	}
	
	protected function getMethodDecorator(\ReflectionMethod $rMethod): ?ApiDescribe {
		return $this->reader->getMethodAnnotation($rMethod, ApiDescribe::class);
	}
}
