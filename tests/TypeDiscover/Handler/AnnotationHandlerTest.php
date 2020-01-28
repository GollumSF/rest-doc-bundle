<?php

namespace Test\GollumSF\RestDocBundle\TypeDiscover\Handler;

use Doctrine\Common\Annotations\Reader;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\TypeDiscover\Handler\AnnotationHandler;
use PHPUnit\Framework\TestCase;

class DummyClass {
	
	private $dummyProp;
	
}


class AnnotationHandlerTest extends TestCase {
	
	public function testGetType() {

		$reader = $this->getMockBuilder(Reader::class)->disableOriginalConstructor()->getMock();
		$modelBuilder = $this->getMockBuilder(ModelBuilderInterface::class)->getMockForAbstractClass();
		
		$annotationHandler = new AnnotationHandler(
			$reader,
			$modelBuilder
		);
		
		$this->assertNull(
			$annotationHandler->getType(\stdClass::class, 'stubName')
		);
	}
}