<?php

namespace Test\GollumSF\RestDocBundle\Unit;

use GollumSF\RestDocBundle\DependencyInjection\Compiler\DoctrineBuilderPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\MetadataBuilderPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\ModelBuilderPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\ParametersGeneratorPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\RequestBodyGeneratorPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\ResponsePropertiesGeneratorPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\SecurityGeneratorPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\TagBuilderPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\TwigBuilderPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\TypeDiscoverPass;
use GollumSF\RestDocBundle\GollumSFRestDocBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GollumSFRestDocBundleTest extends TestCase {

	public function testBuild() {
		$container = $this->createMock(ContainerBuilder::class);

		$container
			->expects($this->exactly(10))
			->method('addCompilerPass')
			->willReturnCallback(function ($pass) use ($container) {
				static $callIndex = 0;
				match ($callIndex++) {
					0 => $this->assertInstanceOf(MetadataBuilderPass::class, $pass),
					1 => $this->assertInstanceOf(TagBuilderPass::class, $pass),
					2 => $this->assertInstanceOf(ModelBuilderPass::class, $pass),
					3 => $this->assertInstanceOf(TypeDiscoverPass::class, $pass),
					4 => $this->assertInstanceOf(ParametersGeneratorPass::class, $pass),
					5 => $this->assertInstanceOf(ResponsePropertiesGeneratorPass::class, $pass),
					6 => $this->assertInstanceOf(SecurityGeneratorPass::class, $pass),
					7 => $this->assertInstanceOf(RequestBodyGeneratorPass::class, $pass),
					8 => $this->assertInstanceOf(DoctrineBuilderPass::class, $pass),
					9 => $this->assertInstanceOf(TwigBuilderPass::class, $pass),
				};
				return $container;
			})
		;

		$bundle = new GollumSFRestDocBundle();
		$bundle->build($container);
	}
}
