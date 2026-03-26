<?php

namespace Test\GollumSF\RestDocBundle\DependencyInjection\Compiler;

use GollumSF\RestDocBundle\Controller\SwaggerUIController;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\TwigBuilderPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Twig\Environment;

class TwigBuilderPassTest extends TestCase {

	public function testProcessWithoutTwig() {
		$container = $this->createMock(ContainerBuilder::class);

		$container
			->expects($this->once())
			->method('hasDefinition')
			->with(Environment::class)
			->willReturn(false)
		;
		$container
			->expects($this->once())
			->method('hasAlias')
			->with(Environment::class)
			->willReturn(false)
		;
		$container
			->expects($this->never())
			->method('getDefinition')
		;

		$pass = new TwigBuilderPass();
		$pass->process($container);
	}

	public function testProcessWithTwigDefinition() {
		$container = $this->createMock(ContainerBuilder::class);
		$definition = $this->createMock(Definition::class);

		$container
			->expects($this->once())
			->method('hasDefinition')
			->with(Environment::class)
			->willReturn(true)
		;
		$container
			->expects($this->once())
			->method('getDefinition')
			->with(SwaggerUIController::class)
			->willReturn($definition)
		;
		$definition
			->expects($this->once())
			->method('addMethodCall')
			->with('setTwig', $this->anything())
		;

		$pass = new TwigBuilderPass();
		$pass->process($container);
	}

	public function testProcessWithTwigAlias() {
		$container = $this->createMock(ContainerBuilder::class);
		$definition = $this->createMock(Definition::class);

		$container
			->expects($this->once())
			->method('hasDefinition')
			->with(Environment::class)
			->willReturn(false)
		;
		$container
			->expects($this->once())
			->method('hasAlias')
			->with(Environment::class)
			->willReturn(true)
		;
		$container
			->expects($this->once())
			->method('getDefinition')
			->with(SwaggerUIController::class)
			->willReturn($definition)
		;
		$definition
			->expects($this->once())
			->method('addMethodCall')
			->with('setTwig', $this->anything())
		;

		$pass = new TwigBuilderPass();
		$pass->process($container);
	}
}
