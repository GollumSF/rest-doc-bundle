<?php

namespace Test\GollumSF\RestDocBundle\DependencyInjection\Compiler;

use GollumSF\RestDocBundle\DependencyInjection\Compiler\SecurityGeneratorPass;
use GollumSF\RestDocBundle\Generator\Security\SecurityGeneratorInterface;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class SecurityGeneratorPassTest extends AbstractCompilerPassTestCase {

	protected function registerCompilerPass(ContainerBuilder $container): void {
		$container->addCompilerPass(new SecurityGeneratorPass());
	}
	
	public function testProcessNot() {

		$serviceTag1 = new Definition();
		$serviceTag1->addTag(SecurityGeneratorInterface::HANDLER_TAG, [ 'priority' => 20 ]);
		$this->setDefinition('serviceTag1', $serviceTag1);

		$serviceTag2 = new Definition();
		$serviceTag2->addTag(SecurityGeneratorInterface::HANDLER_TAG);
		$this->setDefinition('serviceTag2', $serviceTag2);

		$serviceTag3 = new Definition();
		$serviceTag3->addTag(SecurityGeneratorInterface::HANDLER_TAG, [ 'priority' => 10 ]);
		$this->setDefinition('serviceTag3', $serviceTag3);

		$serviceTag4 = new Definition();
		$serviceTag4->addTag(SecurityGeneratorInterface::HANDLER_TAG, [ 'priority' => 20 ]);
		$this->setDefinition('serviceTag4', $serviceTag4);
		
		
		$service = new Definition();
		$this->setDefinition(SecurityGeneratorInterface::class, $service);

		$this->compile();

		$calls = $service->getMethodCalls();

		$this->assertEquals($calls[0][0], 'addHandler');
		$this->assertEquals($calls[0][1][0]->__toString(), 'serviceTag2');

		$this->assertEquals($calls[1][0], 'addHandler');
		$this->assertEquals($calls[1][1][0]->__toString(), 'serviceTag3');

		$this->assertEquals($calls[2][0], 'addHandler');
		$this->assertEquals($calls[2][1][0]->__toString(), 'serviceTag1');

		$this->assertEquals($calls[3][0], 'addHandler');
		$this->assertEquals($calls[3][1][0]->__toString(), 'serviceTag4');
		
	}
	
	public function testProcessNotDeclared() {

		$container = $this->getMockBuilder(ContainerBuilder::class)->disableOriginalConstructor()->getMock();

		$container
			->expects($this->once())
			->method('has')
			->with(SecurityGeneratorInterface::class)
			->willReturn(false);
		;

		$container
			->expects($this->never())
			->method('findTaggedServiceIds')
		;
		
		$compilerPass = new SecurityGeneratorPass();
		$compilerPass->process($container);
	}
}
