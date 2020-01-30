<?php
namespace Test\GollumSF\RestDocBundle\Generator\Parameters;

use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\Parameters\Handler\HandlerInterface;
use GollumSF\RestDocBundle\Generator\Parameters\ParameterCollection;
use GollumSF\RestDocBundle\Generator\Parameters\ParametersGenerator;
use PHPUnit\Framework\TestCase;

class ParametersGeneratorTest extends TestCase {

	use ReflectionPropertyTrait;

	public function testAddDecorator() {
		$handler1 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();
		$handler2 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();
		$handler3 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();

		$parametersGenerator = new ParametersGenerator();

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
		$collection = new ParameterCollection();

		$handler1
			->expects($this->once())
			->method('generateParameter')
			->willReturnCallback(function ($collectionParam, string $url, $metadataParam, string $method) use ($collection, $metadata) {
				$this->assertEquals($collectionParam, $collection);
				$this->assertEquals($metadataParam, $metadata);
				$this->assertEquals($url, 'URL');
				$this->assertEquals($method, 'GET');
			})
		;
		$handler2
			->expects($this->once())
			->method('generateParameter')
			->willReturnCallback(function ($collectionParam, string $url, $metadataParam, string $method) use ($collection, $metadata) {
				$this->assertEquals($collectionParam, $collection);
				$this->assertEquals($metadataParam, $metadata);
				$this->assertEquals($url, 'URL');
				$this->assertEquals($method, 'GET');
			})
		;


		$parametersGenerator = new ParametersGenerator();
		$parametersGenerator->addHandler($handler1);
		$parametersGenerator->addHandler($handler2);

		$this->assertEquals($parametersGenerator->generate('URL', $metadata, 'GET'), $collection);
	}
}