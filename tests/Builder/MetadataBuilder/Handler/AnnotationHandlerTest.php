<?php

namespace Test\GollumSF\RestDocBundle\Builder\MetadataBuilder\Handler;

use Doctrine\Common\Annotations\Reader;
use GollumSF\ControllerActionExtractorBundle\Extractor\ControllerAction;
use GollumSF\ControllerActionExtractorBundle\Extractor\ControllerActionExtractorInterface;
use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestBundle\Annotation\Serialize;
use GollumSF\RestBundle\Annotation\Unserialize;
use GollumSF\RestBundle\Metadata\Serialize\MetadataSerializeManagerInterface;
use GollumSF\RestBundle\Metadata\Unserialize\MetadataUnserializeManagerInterface;
use GollumSF\RestDocBundle\Annotation\ApiDescribe;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\Handler\AnnotationHandler;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class DummyController2 {
	public function action() {}
}

class AnnotationHandlerTest extends TestCase {
	
	use ReflectionPropertyTrait;
	
	public function provideGetDecorator() {
		return [
			[ new ApiDescribe() ],
			[ null ]
		];
	}
	
	/**
	 * @dataProvider provideGetDecorator
	 */
	public function testGetClassDecorator($describe) {
		
		$router = $this->getMockForAbstractClass(RouterInterface::class);
		$metadataSerializeManager = $this->getMockForAbstractClass(MetadataSerializeManagerInterface::class);
		$metadataUnserializeManager = $this->getMockForAbstractClass(MetadataUnserializeManagerInterface::class);
		$reader = $this->getMockBuilder(Reader::class)->disableOriginalConstructor()->getMock();
		$controllerActionExtractor = $this->getMockBuilder(ControllerActionExtractorInterface::class)->getMockForAbstractClass();
		
		$rClass = new \ReflectionClass(DummyController2::class);
		
		$reader
			->expects($this->once())
			->method('getClassAnnotation')
			->willReturnCallback(function ($rClass, $annoClass) use ($describe) {
				$this->assertInstanceOf(\ReflectionClass::class, $rClass);
				$this->assertEquals($rClass->getName(), DummyController2::class);
				$this->assertEquals($annoClass, ApiDescribe::class);
				return $describe;
			});
		
		$handler = new AnnotationHandler(
			$router,
			$controllerActionExtractor,
			$metadataSerializeManager,
			$metadataUnserializeManager,
			$reader
		);
		
		$this->assertEquals(
			$this->reflectionCallMethod($handler, 'getClassDecorator', [ $rClass ]),
			$describe
		);
	}
	
	/**
	 * @dataProvider provideGetDecorator
	 */
	public function testGetMethodDecorator($describe) {
		
		$router = $this->getMockForAbstractClass(RouterInterface::class);
		$metadataSerializeManager = $this->getMockForAbstractClass(MetadataSerializeManagerInterface::class);
		$metadataUnserializeManager = $this->getMockForAbstractClass(MetadataUnserializeManagerInterface::class);
		$reader = $this->getMockBuilder(Reader::class)->disableOriginalConstructor()->getMock();
		$controllerActionExtractor = $this->getMockBuilder(ControllerActionExtractorInterface::class)->getMockForAbstractClass();
		
		$rClass = new \ReflectionClass(DummyController2::class);
		$rMethod = $rClass->getMethod('action');
		
		$reader
			->expects($this->once())
			->method('getMethodAnnotation')
			->willReturnCallback(function ($rMethod, $annoClass) use ($describe) {
				$this->assertInstanceOf(\ReflectionMethod::class, $rMethod);
				$this->assertEquals($rMethod->getName(), 'action');
				$this->assertEquals($annoClass, ApiDescribe::class);
				return $describe;
			});
		
		$handler = new AnnotationHandler(
			$router,
			$controllerActionExtractor,
			$metadataSerializeManager,
			$metadataUnserializeManager,
			$reader
		);
		
		$this->assertEquals(
			$this->reflectionCallMethod($handler, 'getMethodDecorator', [ $rMethod ]),
			$describe
		);
	}
}
