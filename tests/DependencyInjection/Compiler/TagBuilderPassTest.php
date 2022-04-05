<?php

namespace Test\GollumSF\RestDocBundle\DependencyInjection\Compiler;

use GollumSF\RestDocBundle\Builder\TagBuilder\TagBuilderInterface;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\TagBuilderPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class TagBuilderPassTest extends AbstractCompilerPassTestCase {

	protected function registerCompilerPass(ContainerBuilder $container): void {
		$container->addCompilerPass(new TagBuilderPass());
	}
	
	public function testProcessNot() {

		$serviceTag1 = new Definition();
		$serviceTag1->addTag(TagBuilderInterface::DECORATOR_TAG, [ 'priority' => 20 ]);
		$this->setDefinition('serviceTag1', $serviceTag1);

		$serviceTag2 = new Definition();
		$serviceTag2->addTag(TagBuilderInterface::DECORATOR_TAG);
		$this->setDefinition('serviceTag2', $serviceTag2);

		$serviceTag3 = new Definition();
		$serviceTag3->addTag(TagBuilderInterface::DECORATOR_TAG, [ 'priority' => 10 ]);
		$this->setDefinition('serviceTag3', $serviceTag3);

		$serviceTag4 = new Definition();
		$serviceTag4->addTag(TagBuilderInterface::DECORATOR_TAG, [ 'priority' => 20 ]);
		$this->setDefinition('serviceTag4', $serviceTag4);
		
		
		$service = new Definition();
		$this->setDefinition(TagBuilderInterface::class, $service);

		$this->compile();

		$calls = $service->getMethodCalls();

		$this->assertEquals($calls[0][0], 'addDecorator');
		$this->assertEquals($calls[0][1][0]->__toString(), 'serviceTag2');

		$this->assertEquals($calls[1][0], 'addDecorator');
		$this->assertEquals($calls[1][1][0]->__toString(), 'serviceTag3');

		$this->assertEquals($calls[2][0], 'addDecorator');
		$this->assertEquals($calls[2][1][0]->__toString(), 'serviceTag1');

		$this->assertEquals($calls[3][0], 'addDecorator');
		$this->assertEquals($calls[3][1][0]->__toString(), 'serviceTag4');
		
	}
	
	public function testProcessNotDeclared() {

		$container = $this->getMockBuilder(ContainerBuilder::class)->disableOriginalConstructor()->getMock();

		$container
			->expects($this->once())
			->method('has')
			->with(TagBuilderInterface::class)
			->willReturn(false);
		;

		$container
			->expects($this->never())
			->method('findTaggedServiceIds')
		;
		
		$compilerPass = new TagBuilderPass();
		$compilerPass->process($container);
	}
}
