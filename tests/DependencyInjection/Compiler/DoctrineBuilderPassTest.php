<?php

namespace Test\GollumSF\RestDocBundle\DependencyInjection\Compiler;

use Doctrine\Persistence\ManagerRegistry;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\DoctrineBuilderPass;
use GollumSF\RestDocBundle\TypeDiscover\Handler\DoctrineHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class DoctrineBuilderPassTest extends TestCase {

	public function testProcessWithoutDoctrine() {
		$container = $this->createMock(ContainerBuilder::class);

		$container
			->expects($this->once())
			->method('hasDefinition')
			->with(ManagerRegistry::class)
			->willReturn(false)
		;
		$container
			->expects($this->once())
			->method('hasAlias')
			->with(ManagerRegistry::class)
			->willReturn(false)
		;
		$container
			->expects($this->never())
			->method('getDefinition')
		;

		$pass = new DoctrineBuilderPass();
		$pass->process($container);
	}

	public function testProcessWithDoctrineDefinition() {
		$container = $this->createMock(ContainerBuilder::class);
		$definition = $this->createMock(Definition::class);

		$container
			->expects($this->once())
			->method('hasDefinition')
			->with(ManagerRegistry::class)
			->willReturn(true)
		;
		$container
			->expects($this->once())
			->method('getDefinition')
			->with(DoctrineHandler::class)
			->willReturn($definition)
		;
		$definition
			->expects($this->once())
			->method('addMethodCall')
			->with('setManagerRegistry', $this->anything())
		;

		$pass = new DoctrineBuilderPass();
		$pass->process($container);
	}

	public function testProcessWithDoctrineAlias() {
		$container = $this->createMock(ContainerBuilder::class);
		$definition = $this->createMock(Definition::class);

		$container
			->expects($this->once())
			->method('hasDefinition')
			->with(ManagerRegistry::class)
			->willReturn(false)
		;
		$container
			->expects($this->once())
			->method('hasAlias')
			->with(ManagerRegistry::class)
			->willReturn(true)
		;
		$container
			->expects($this->once())
			->method('getDefinition')
			->with(DoctrineHandler::class)
			->willReturn($definition)
		;
		$definition
			->expects($this->once())
			->method('addMethodCall')
			->with('setManagerRegistry', $this->anything())
		;

		$pass = new DoctrineBuilderPass();
		$pass->process($container);
	}
}
