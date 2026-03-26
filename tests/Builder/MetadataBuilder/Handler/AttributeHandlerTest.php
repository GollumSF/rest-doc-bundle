<?php

namespace Test\GollumSF\RestDocBundle\Builder\MetadataBuilder\Handler;

use Doctrine\Common\Annotations\Reader;
use GollumSF\ControllerActionExtractorBundle\Extractor\ControllerActionExtractorInterface;
use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestBundle\Metadata\Serialize\MetadataSerializeManagerInterface;
use GollumSF\RestBundle\Metadata\Unserialize\MetadataUnserializeManagerInterface;
use GollumSF\RestDocBundle\Annotation\ApiDescribe;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\Handler\AttributeHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;
use PHPUnit\Framework\Attributes\DataProvider;

class AnnoDummyNull {
	public function action() {}
}

#[ApiDescribe('CLASS')]
class AnnoDummyClass {
	public function action() {}
}

class AnnoDummyMethod {
	#[ApiDescribe('METHOD')]
	public function action() {}
}


class AttributeHandlerTest extends TestCase {
	
	use ReflectionPropertyTrait;
	
	public static function provideGetClassDecorator() {
		return [
			[ AnnoDummyClass::class, 'CLASS' ],
			[ AnnoDummyNull::class, null ]
		];
	}
	
	#[DataProvider('provideGetClassDecorator')]
	public function testGetClassDecorator($class, $name) {
		
		$router = $this->createMock(RouterInterface::class);
		$metadataSerializeManager = $this->createMock(MetadataSerializeManagerInterface::class);
		$metadataUnserializeManager = $this->createMock(MetadataUnserializeManagerInterface::class);
		$controllerActionExtractor = $this->createMock(ControllerActionExtractorInterface::class);
		
		$rClass = new \ReflectionClass($class);
		
		$handler = new AttributeHandler(
			$router,
			$controllerActionExtractor,
			$metadataSerializeManager,
			$metadataUnserializeManager
		);
		
		/** @var ApiDescribe $describe */
		$describe = $this->reflectionCallMethod($handler, 'getClassDecorator', [ $rClass ]);
		if ($name === null) {
			$this->assertNull($describe);
		} else {
			$this->assertEquals($describe->getEntity(), $name);
		}
	}
	
	public static function provideGetMethodDecorator() {
		return [
			[ AnnoDummyMethod::class, 'METHOD' ],
			[ AnnoDummyNull::class, null ]
		];
	}
	
	#[DataProvider('provideGetMethodDecorator')]
	public function testGetMethodDecorator($class, $name) {
		
		$router = $this->createMock(RouterInterface::class);
		$metadataSerializeManager = $this->createMock(MetadataSerializeManagerInterface::class);
		$metadataUnserializeManager = $this->createMock(MetadataUnserializeManagerInterface::class);
		$controllerActionExtractor = $this->createMock(ControllerActionExtractorInterface::class);
		
		$rClass = new \ReflectionClass($class);
		$rMethod = $rClass->getMethod('action');
		
		$handler = new AttributeHandler(
			$router,
			$controllerActionExtractor,
			$metadataSerializeManager,
			$metadataUnserializeManager
		);
		
		/** @var ApiDescribe $describe */
		$describe = $this->reflectionCallMethod($handler, 'getMethodDecorator', [ $rMethod ]);
		if ($name === null) {
			$this->assertNull($describe);
		} else {
			$this->assertEquals($describe->getEntity(), $name);
		}
	}
}
