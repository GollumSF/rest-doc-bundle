<?php
namespace Test\GollumSF\RestDocBundle\Generator\ResponseBody;

use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\ResponseBody\Handler\HandlerInterface;
use GollumSF\RestDocBundle\Generator\ResponseBody\ResponseBodyGenerator;
use GollumSF\RestDocBundle\Generator\ResponseBody\ResponseBodyPropertyCollection;
use PHPUnit\Framework\TestCase;

class ResponseBodyGeneratorTest extends TestCase {

	use ReflectionPropertyTrait;

	public function testAddHandler() {
		$handler1 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();
		$handler2 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();
		$handler3 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();

		$parametersGenerator = new ResponseBodyGenerator();

		$parametersGenerator->addHandler($handler1);
		$this->assertEquals($this->reflectionGetValue($parametersGenerator, 'handlers'), [ $handler1 ]);
		$parametersGenerator->addHandler($handler2);
		$this->assertEquals($this->reflectionGetValue($parametersGenerator, 'handlers'), [ $handler1, $handler2 ]);
		$parametersGenerator->addHandler($handler3);
		$this->assertEquals($this->reflectionGetValue($parametersGenerator, 'handlers'), [ $handler1, $handler2, $handler3 ]);
	}

	public function providerHasRequestBody() {
		return [
			[ false, false, false ],
			[ true, false, true ],
			[ false, true, true ],
			[ true, true, true ],
		];
	}

	/**
	 * @dataProvider providerHasRequestBody
	 */
	public function testHasResponseBody($first, $second, $result) {

		$handler1 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();
		$handler2 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();

		$handler1
			->method('hasResponseBody')
			->with($metadata, 'GET')
			->willReturn($first)
		;
		$handler2
			->method('hasResponseBody')
			->with($metadata, 'GET')
			->willReturn($second)
		;

		$requestBodyGenerator = new ResponseBodyGenerator();
		$requestBodyGenerator->addHandler($handler1);
		$requestBodyGenerator->addHandler($handler2);

		$this->assertEquals(
			$this->reflectionCallMethod($requestBodyGenerator, 'hasResponseBody', [ $metadata, 'GET' ]), $result
		);

	}

	public function testGenerateProperties() {
		$handler1 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();
		$handler2 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$collection = new ResponseBodyPropertyCollection();


		$handler1
			->expects($this->at(0))
			->method('hasResponseBody')
			->with($metadata, 'GET')
			->willReturn(true)
		;
		$handler1
			->expects($this->at(1))
			->method('generateProperties')
			->willReturnCallback(function ($collectionParam, $metadataParam, string $method) use ($collection, $metadata) {
				$this->assertEquals($collectionParam, $collection);
				$this->assertEquals($metadataParam, $metadata);
				$this->assertEquals($method, 'GET');
			})
		;
		$handler2
			->expects($this->once())
			->method('hasResponseBody')
			->with($metadata, 'GET')
			->willReturn(false)
		;
		$handler2
			->expects($this->never())
			->method('generateProperties')
		;
		
		$responsePropertiesGenerator = new ResponseBodyGenerator();
		$responsePropertiesGenerator->addHandler($handler1);
		$responsePropertiesGenerator->addHandler($handler2);

		$this->assertEquals($responsePropertiesGenerator->generateProperties($metadata, 'GET'), $collection);
	}
}