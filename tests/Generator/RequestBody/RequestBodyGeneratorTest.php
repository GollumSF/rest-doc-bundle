<?php
namespace Test\GollumSF\RestDocBundle\Generator\RequestBody;

use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\RequestBody\Handler\HandlerInterface;
use GollumSF\RestDocBundle\Generator\RequestBody\ParameterCollection;
use GollumSF\RestDocBundle\Generator\RequestBody\RequestBodyGenerator;
use GollumSF\RestDocBundle\Generator\RequestBody\RequestBodyPropertyCollection;
use PHPUnit\Framework\TestCase;

class RequestBodyGeneratorTest extends TestCase {

	use ReflectionPropertyTrait;

	public function testAddHandler() {
		$handler1 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();
		$handler2 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();
		$handler3 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();

		$requestBodyGenerator = new RequestBodyGenerator();

		$requestBodyGenerator->addHandler($handler1);
		$this->assertEquals($this->reflectionGetValue($requestBodyGenerator, 'handlers'), [ $handler1 ]);
		$requestBodyGenerator->addHandler($handler2);
		$this->assertEquals($this->reflectionGetValue($requestBodyGenerator, 'handlers'), [ $handler1, $handler2 ]);
		$requestBodyGenerator->addHandler($handler3);
		$this->assertEquals($this->reflectionGetValue($requestBodyGenerator, 'handlers'), [ $handler1, $handler2, $handler3 ]);
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
	public function testHasRequestBody($first, $second, $result) {

		$handler1 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();
		$handler2 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();

		$handler1
			->method('hasRequestBody')
			->with($metadata, 'GET')
			->willReturn($first)
		;
		$handler2
			->method('hasRequestBody')
			->with($metadata, 'GET')
			->willReturn($second)
		;

		$requestBodyGenerator = new RequestBodyGenerator();
		$requestBodyGenerator->addHandler($handler1);
		$requestBodyGenerator->addHandler($handler2);

		$this->assertEquals(
			$this->reflectionCallMethod($requestBodyGenerator, 'hasRequestBody', [ $metadata, 'GET' ]), $result
		);
			
	}

	public function testGenerateParameter() {
		$handler1 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();
		$handler2 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$collection = new RequestBodyPropertyCollection();

		$handler1
			->expects($this->once())
			->method('generateProperties')
			->willReturnCallback(function ($collectionParam, $metadataParam, string $method) use ($collection, $metadata) {
				$this->assertEquals($collectionParam, $collection);
				$this->assertEquals($metadataParam, $metadata);
				$this->assertEquals($method, 'GET');
			})
		;
		$handler1
			->method('hasRequestBody')
			->with($metadata, 'GET')
			->willReturn(true)
		;
		$handler2
			->expects($this->once())
			->method('generateProperties')
			->willReturnCallback(function ($collectionParam, $metadataParam, string $method) use ($collection, $metadata) {
				$this->assertEquals($collectionParam, $collection);
				$this->assertEquals($metadataParam, $metadata);
				$this->assertEquals($method, 'GET');
			})
		;
		$handler2
			->method('hasRequestBody')
			->with($metadata, 'GET')
			->willReturn(false)
		;

		$requestBodyGenerator = new RequestBodyGenerator();
		$requestBodyGenerator->addHandler($handler1);
		$requestBodyGenerator->addHandler($handler2);

		$this->assertEquals($requestBodyGenerator->generateProperties($metadata, 'GET'), $collection);
	}
}