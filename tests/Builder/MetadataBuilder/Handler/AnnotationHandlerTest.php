<?php

namespace Test\GollumSF\RestDocBundle\Builder\MetadataBuilder\Handler;

use Doctrine\Common\Annotations\Reader;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\Handler\AnnotationHandler;
use GollumSF\RestDocBundle\Reflection\ControllerActionExtractorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;

class DummyClass {
	
	private $dummyProp;
	
}


class AnnotationHandlerTest extends TestCase {
	
	public function testGetType() {

		$router = $this->getMockBuilder(RouterInterface::class)->getMockForAbstractClass();
		$reader = $this->getMockBuilder(Reader::class)->disableOriginalConstructor()->getMock();
		$controllerActionExtractor = $this->getMockBuilder(ControllerActionExtractorInterface::class)->getMockForAbstractClass();
		
		$annotationHandler = new AnnotationHandler(
			$router,
			$reader,
			$controllerActionExtractor
		);
		
		$this->assertTrue(true);
	}

}