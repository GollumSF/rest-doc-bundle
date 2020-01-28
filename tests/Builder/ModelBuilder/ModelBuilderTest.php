<?php

namespace GollumSF\RestDocBundle\Builder\ModelBuilder;

use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestDocBundle\Builder\ModelBuilder\Decorator\DecoratorInterface;
use GollumSF\RestDocBundle\TypeDiscover\Models\ObjectType;
use PHPUnit\Framework\TestCase;

class ModelBuildeTest extends TestCase {
	
	use ReflectionPropertyTrait;
	
	public function testAddDecorator() {
		$decorator1 = $this->getMockBuilder(DecoratorInterface::class)->getMockForAbstractClass();
		$decorator2 = $this->getMockBuilder(DecoratorInterface::class)->getMockForAbstractClass();
		$decorator3 = $this->getMockBuilder(DecoratorInterface::class)->getMockForAbstractClass();
		
		$modelBuilder = new ModelBuilder();

		$modelBuilder->addDecorator($decorator1);
		$this->assertEquals($this->reflectionGetValue($modelBuilder, 'decorators'), [ $decorator1 ]);
		$modelBuilder->addDecorator($decorator2);
		$this->assertEquals($this->reflectionGetValue($modelBuilder, 'decorators'), [ $decorator1, $decorator2 ]);
		$modelBuilder->addDecorator($decorator3);
		$this->assertEquals($this->reflectionGetValue($modelBuilder, 'decorators'), [ $decorator1, $decorator2, $decorator3 ]);
	}
	
	public function testGetModel() {
		$decorator1 = $this->getMockBuilder(DecoratorInterface::class)->getMockForAbstractClass();
		$decorator2 = $this->getMockBuilder(DecoratorInterface::class)->getMockForAbstractClass();

		$model2 = new ObjectType(\stdClass::class);
		$model3 = new ObjectType(\stdClass::class);
		
		$decorator1
			->expects($this->once())
			->method('decorateModel')
			->willReturnCallback(function ($model1) use ($model2) {
				$this->assertInstanceOf(ObjectType::class, $model1);
				$this->assertEquals($model1->getClass(), \stdClass::class);
				return $model2;
			})
		;
		$decorator2
			->expects($this->once())
			->method('decorateModel')
			->with($model2)
			->willReturn($model3)
		;
		
		$modelBuilder = new ModelBuilder();
		$modelBuilder->addDecorator($decorator1);
		$modelBuilder->addDecorator($decorator2);
		
		$this->assertEquals($this->reflectionGetValue($modelBuilder, 'models'), []);

		$this->assertEquals($modelBuilder->getModel(\stdClass::class), $model3);

		$this->assertEquals($this->reflectionGetValue($modelBuilder, 'models'), [ \stdClass::class => $model3 ]);
		
		$this->assertEquals($modelBuilder->getModel(\stdClass::class), $model3);


		$this->assertEquals($modelBuilder->getAllModels(), [ \stdClass::class => $model3 ]);
	}
	
}