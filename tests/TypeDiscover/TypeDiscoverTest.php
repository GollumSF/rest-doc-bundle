<?php

namespace Test\GollumSF\RestDocBundle\TypeDiscover;

use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestDocBundle\TypeDiscover\Handler\HandlerInterface;
use GollumSF\RestDocBundle\TypeDiscover\Models\NativeType;
use GollumSF\RestDocBundle\TypeDiscover\Models\TypeInterface;
use GollumSF\RestDocBundle\TypeDiscover\TypeDiscover;
use PHPUnit\Framework\TestCase;

class TypeDiscoverTest extends TestCase {

	use ReflectionPropertyTrait;

	public function testAddHandler() {
		$handler1 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();
		$handler2 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();
		$handler3 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();

		$typeDiscover = new TypeDiscover();

		$typeDiscover->addHandler($handler1);
		$this->assertEquals($this->reflectionGetValue($typeDiscover, 'handlers'), [ $handler1 ]);
		$typeDiscover->addHandler($handler2);
		$this->assertEquals($this->reflectionGetValue($typeDiscover, 'handlers'), [ $handler1, $handler2 ]);
		$typeDiscover->addHandler($handler3);
		$this->assertEquals($this->reflectionGetValue($typeDiscover, 'handlers'), [ $handler1, $handler2, $handler3 ]);
	}

	public function testGenerateParameter() {
		$handler1 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();
		$handler2 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();

		$type = $this->getMockForAbstractClass(TypeInterface::class);

		$handler1
			->expects($this->once())
			->method('getType')
			->with(\stdClass::class, 'TargetName')
			->willReturn(null)
		;
		$handler2
			->expects($this->once())
			->method('getType')
			->with(\stdClass::class, 'TargetName')
			->willReturn($type)
		;


		$typeDiscover = new TypeDiscover();
		$typeDiscover->addHandler($handler1);
		$typeDiscover->addHandler($handler2);

		$this->assertEquals($typeDiscover->getType(\stdClass::class, 'TargetName'), $type);
	}

	public function testGenerateParameterMixed() {
		$handler1 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();
		$handler2 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();

		$handler1
			->expects($this->once())
			->method('getType')
			->with(\stdClass::class, 'TargetName')
			->willReturn(null)
		;
		$handler2
			->expects($this->once())
			->method('getType')
			->with(\stdClass::class, 'TargetName')
			->willReturn(null)
		;


		$typeDiscover = new TypeDiscover();
		$typeDiscover->addHandler($handler1);
		$typeDiscover->addHandler($handler2);

		$result = $typeDiscover->getType(\stdClass::class, 'TargetName');

		$this->assertInstanceOf(NativeType::class, $result);
		$this->assertEquals($result->getType(), 'mixed');
	}
}