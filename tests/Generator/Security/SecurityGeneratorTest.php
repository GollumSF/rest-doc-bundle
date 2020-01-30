<?php
namespace Test\GollumSF\RestDocBundle\Generator\Security;

use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Generator\Security\Handler\HandlerInterface;
use GollumSF\RestDocBundle\Generator\Security\SecurityGenerator;
use GollumSF\RestDocBundle\Generator\Security\SecurityCollection;
use PHPUnit\Framework\TestCase;

class SecurityGeneratorTest extends TestCase {

	use ReflectionPropertyTrait;

	public function testAddHandler() {
		$handler1 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();
		$handler2 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();
		$handler3 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();

		$parametersGenerator = new SecurityGenerator();

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
		$collection = new SecurityCollection();

		$handler1
			->expects($this->once())
			->method('generateSecurities')
			->with($collection)
		;
		$handler2
			->expects($this->once())
			->method('generateSecurities')
			->with($collection)
		;


		$securityGenerator = new SecurityGenerator();
		$securityGenerator->addHandler($handler1);
		$securityGenerator->addHandler($handler2);

		$this->assertEquals($securityGenerator->generate(), $collection);
	}
}