<?php

namespace Test\GollumSF\RestDocBundle\Builder\TagBuilder\Decorator;

use GollumSF\RestDocBundle\Annotation\ApiEntity;
use GollumSF\RestDocBundle\Builder\TagBuilder\Decorator\AbstractDecoratorDecorator;
use GollumSF\RestDocBundle\Builder\TagBuilder\Tag;
use PHPUnit\Framework\TestCase;

class AbstractDecoratorDecoratorMockAbstract extends AbstractDecoratorDecorator {
	
	/** @var AbstractDecoratorDecorator */
	private $mock;
	
	public function __construct(
		AbstractDecoratorDecorator $mock
	) {
		$this->mock = $mock;
	}
	
	protected function getClassDecorator(\ReflectionClass $rClass): ?ApiEntity {
		return $this->mock->getClassDecorator($rClass);
	}
}

class AbstractDecoratorDecoratorTest extends TestCase {
	
	public function providerDecorateTag() {
		return [
			[null, null, null, null ],
			[ new ApiEntity([]), null, null, null ],
			[ new ApiEntity([ 'url' => 'URL' ]), 'URL', null, null ],
			[ new ApiEntity([ 'description' => 'DESCRIPTION' ]), null, 'DESCRIPTION', null ],
			[ new ApiEntity([ 'docDescription' => 'DOC_DESCRIPTION' ]), null, null, 'DOC_DESCRIPTION' ],
			[ new ApiEntity([
				'description' => 'DESCRIPTION',
				'url' => 'URL',
				'docDescription' => 'DOC_DESCRIPTION',
			]), 'URL', 'DESCRIPTION', 'DOC_DESCRIPTION' ],
		];
	}

	/**
	 * @dataProvider providerDecorateTag
	 */
	public function testDecorateTag($annoApiEntity, $url, $description, $docDescription) {
		
		$mock = $this->getMockForAbstractClass(AbstractDecoratorDecorator::class);
		
		$tag = new Tag(\stdClass::class);
		
		$mock
			->expects($this->once())
			->method('getClassDecorator')
			->willReturnCallback(function ($rClass) use ($annoApiEntity) {
				$this->assertInstanceOf(\ReflectionClass::class, $rClass);
				$this->assertEquals($rClass->getName(), \stdClass::class);
				return $annoApiEntity;
			});
		
		$decorator = new AbstractDecoratorDecoratorMockAbstract($mock);
		
		$this->assertEquals($decorator->decorateTag($tag), $tag);

		$this->assertEquals($tag->getUrl(), $url);
		$this->assertEquals($tag->getDescription(),$description);
		$this->assertEquals($tag->getDocDescription(), $docDescription);
	}
}
