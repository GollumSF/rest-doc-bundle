<?php
namespace Test\GollumSF\RestDocBundle;

use GollumSF\RestDocBundle\DependencyInjection\Compiler\MetadataBuilderPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\ModelBuilderPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\ParametersGeneratorPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\RequestBodyGeneratorPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\ResponsePropertiesGeneratorPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\SecurityGeneratorPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\TagBuilderPass;
use GollumSF\RestDocBundle\DependencyInjection\Compiler\TypeDiscoverPass;
use GollumSF\RestDocBundle\GollumSFRestDocBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GollumSFRestDocBundletTest extends TestCase {
	
	public function testBuild() {

		$container = $this->getMockBuilder(ContainerBuilder::class)->disableOriginalConstructor()->getMock();

		$container->expects($this->at(0))->method('addCompilerPass')->willReturnCallback(function ($pass) { $this->assertInstanceOf(MetadataBuilderPass::class            , $pass); });
		$container->expects($this->at(1))->method('addCompilerPass')->willReturnCallback(function ($pass) { $this->assertInstanceOf(TagBuilderPass::class                 , $pass); });
		$container->expects($this->at(2))->method('addCompilerPass')->willReturnCallback(function ($pass) { $this->assertInstanceOf(ModelBuilderPass::class               , $pass); });
		$container->expects($this->at(3))->method('addCompilerPass')->willReturnCallback(function ($pass) { $this->assertInstanceOf(TypeDiscoverPass::class               , $pass); });
		$container->expects($this->at(4))->method('addCompilerPass')->willReturnCallback(function ($pass) { $this->assertInstanceOf(ParametersGeneratorPass::class        , $pass); });
		$container->expects($this->at(5))->method('addCompilerPass')->willReturnCallback(function ($pass) { $this->assertInstanceOf(ResponsePropertiesGeneratorPass::class, $pass); });
		$container->expects($this->at(6))->method('addCompilerPass')->willReturnCallback(function ($pass) { $this->assertInstanceOf(SecurityGeneratorPass::class          , $pass); });
		$container->expects($this->at(7))->method('addCompilerPass')->willReturnCallback(function ($pass) { $this->assertInstanceOf(RequestBodyGeneratorPass::class       , $pass); });
		
		$bundle = new GollumSFRestDocBundle();
		$bundle->build($container);
	}
}
