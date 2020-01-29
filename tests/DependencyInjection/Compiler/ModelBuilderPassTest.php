<?php

namespace Test\GollumSF\RestDocBundle\DependencyInjection\Compiler;

use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilder;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\ModelBuilderPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ModelBuilderPassTest extends AbstractCompilerPassTestCase {

	protected function registerCompilerPass(ContainerBuilder $container): void {
		$container->addCompilerPass(new ModelBuilderPass());
	}
	
	public function testProcessNot() {

		$serviceTag1 = new Definition();
		$serviceTag1->addTag(ModelBuilderInterface::DECORATOR_TAG);
		$this->setDefinition('serviceTag1', $serviceTag1);

		$serviceTag2 = new Definition();
		$serviceTag2->addTag(ModelBuilderInterface::DECORATOR_TAG);
		$this->setDefinition('serviceTag2', $serviceTag2);
		
		
		$service = new Definition();
		$this->setDefinition(ModelBuilderInterface::class, $service);

		$this->compile();

		$calls = $service->getMethodCalls();

		$this->assertEquals($calls[0][0], 'addDecorator');
		$this->assertEquals($calls[0][1][0]->__toString(), 'serviceTag1');

		$this->assertEquals($calls[1][0], 'addDecorator');
		$this->assertEquals($calls[1][1][0]->__toString(), 'serviceTag2');
		
	}
	
	public function testProcessNotDeclared() {

		$container = $this->getMockBuilder(ContainerBuilder::class)->disableOriginalConstructor()->getMock();

		$container
			->expects($this->once())
			->method('has')
			->with(ModelBuilderInterface::class)
			->willReturn(false);
		;

		$container
			->expects($this->never())
			->method('findTaggedServiceIds')
		;
		
		$compilerPass = new ModelBuilderPass();
		$compilerPass->process($container);
	}
}
