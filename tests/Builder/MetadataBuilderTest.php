<?php

namespace Test\GollumSF\RestDocBundle\Builder;

use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\Handler\HandlerInterface;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\MetadataBuilder;
use PHPUnit\Framework\TestCase;

class MetadataBuilderTest extends TestCase {
	
	use ReflectionPropertyTrait;
	
	public function testAddHandler() {

		$handler1 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();
		$handler2 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();
		$handler3 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();

		$metadataBuilder = new MetadataBuilder();

		$metadataBuilder->addHandler($handler1);
		$this->assertEquals($this->reflectionGetValue($metadataBuilder, 'handlers'), [ $handler1 ]);
		
		$metadataBuilder->addHandler($handler2);
		$this->assertEquals($this->reflectionGetValue($metadataBuilder, 'handlers'), [ $handler1, $handler2 ]);
		
		$metadataBuilder->addHandler($handler3);
		$this->assertEquals($this->reflectionGetValue($metadataBuilder, 'handlers'), [ $handler1, $handler2, $handler3 ]);		
	}


	public function testGetMetadataCollection() {

		$handler1 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();
		$handler2 = $this->getMockBuilder(HandlerInterface::class)->getMockForAbstractClass();

		$handler1
			->method('getMetadataCollection')
			->willReturn(['METADATA1'])
		;
		$handler2
			->method('getMetadataCollection')
			->willReturn(['METADATA2', 'METADATA3'])
		;
		
		$metadataBuilder = new MetadataBuilder();
		$metadataBuilder->addHandler($handler1);
		$metadataBuilder->addHandler($handler2);

		$this->assertNull($this->reflectionGetValue($metadataBuilder, 'cacheMetadataCollection'));
		$this->assertEquals($metadataBuilder->getMetadataCollection(), [ 'METADATA1', 'METADATA2', 'METADATA3' ]);
		$this->assertEquals($this->reflectionGetValue($metadataBuilder, 'cacheMetadataCollection'), [ 'METADATA1', 'METADATA2', 'METADATA3' ]);
		$this->assertEquals($metadataBuilder->getMetadataCollection(), [ 'METADATA1', 'METADATA2', 'METADATA3' ]);
		
	}
	
}