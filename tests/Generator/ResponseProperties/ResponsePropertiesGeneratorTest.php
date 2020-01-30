<?php
namespace Test\GollumSF\RestDocBundle\Generator\ResponseProperties;

use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\ResponseProperties\Handler\HandlerInterface;
use GollumSF\RestDocBundle\Generator\ResponseProperties\ResponsePropertiesGenerator;
use GollumSF\RestDocBundle\Generator\ResponseProperties\ResponsePropertyCollection;
use PHPUnit\Framework\TestCase;

class ResponsePropertiesGeneratorTest extends TestCase {

	use ReflectionPropertyTrait;

	public function testAddDecorator() {
		$handler1 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();
		$handler2 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();
		$handler3 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();

		$parametersGenerator = new ResponsePropertiesGenerator();

		$parametersGenerator->addHandler($handler1);
		$this->assertEquals($this->reflectionGetValue($parametersGenerator, 'handlers'), [ $handler1 ]);
		$parametersGenerator->addHandler($handler2);
		$this->assertEquals($this->reflectionGetValue($parametersGenerator, 'handlers'), [ $handler1, $handler2 ]);
		$parametersGenerator->addHandler($handler3);
		$this->assertEquals($this->reflectionGetValue($parametersGenerator, 'handlers'), [ $handler1, $handler2, $handler3 ]);
	}

	public function testGenerateParameter() {
		$handler1 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();
		$handler2 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$collection = new ResponsePropertyCollection();

		$handler1
			->expects($this->once())
			->method('generateResponseProperties')
			->willReturnCallback(function ($collectionParam, $metadataParam, string $method) use ($collection, $metadata) {
				$this->assertEquals($collectionParam, $collection);
				$this->assertEquals($metadataParam, $metadata);
				$this->assertEquals($method, 'GET');
			})
		;
		$handler2
			->expects($this->once())
			->method('generateResponseProperties')
			->willReturnCallback(function ($collectionParam, $metadataParam, string $method) use ($collection, $metadata) {
				$this->assertEquals($collectionParam, $collection);
				$this->assertEquals($metadataParam, $metadata);
				$this->assertEquals($method, 'GET');
			})
		;


		$responsePropertiesGenerator = new ResponsePropertiesGenerator();
		$responsePropertiesGenerator->addHandler($handler1);
		$responsePropertiesGenerator->addHandler($handler2);

		$this->assertEquals($responsePropertiesGenerator->generate($metadata, 'GET'), $collection);
	}
}