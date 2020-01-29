<?php

namespace Test\GollumSF\RestDocBundle\Builder\TagBuilder;

use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestDocBundle\Builder\TagBuilder\Decorator\DecoratorInterface;
use GollumSF\RestDocBundle\Builder\TagBuilder\Tag;
use GollumSF\RestDocBundle\Builder\TagBuilder\TagBuilder;
use PHPUnit\Framework\TestCase;

class TagBuilderTest extends TestCase {

	use ReflectionPropertyTrait;

	public function testAddDecorator() {
		$decorator1 = $this->getMockBuilder(DecoratorInterface::class)->getMockForAbstractClass();
		$decorator2 = $this->getMockBuilder(DecoratorInterface::class)->getMockForAbstractClass();
		$decorator3 = $this->getMockBuilder(DecoratorInterface::class)->getMockForAbstractClass();

		$tagBuilder = new TagBuilder();

		$tagBuilder->addDecorator($decorator1);
		$this->assertEquals($this->reflectionGetValue($tagBuilder, 'decorators'), [ $decorator1 ]);
		$tagBuilder->addDecorator($decorator2);
		$this->assertEquals($this->reflectionGetValue($tagBuilder, 'decorators'), [ $decorator1, $decorator2 ]);
		$tagBuilder->addDecorator($decorator3);
		$this->assertEquals($this->reflectionGetValue($tagBuilder, 'decorators'), [ $decorator1, $decorator2, $decorator3 ]);
	}

	public function testGetTag() {
		$decorator1 = $this->getMockBuilder(DecoratorInterface::class)->getMockForAbstractClass();
		$decorator2 = $this->getMockBuilder(DecoratorInterface::class)->getMockForAbstractClass();

		$tag2 = new Tag(\stdClass::class);
		$tag3 = new Tag(\stdClass::class);

		$decorator1
			->expects($this->once())
			->method('decorateTag')
			->willReturnCallback(function ($tag1) use ($tag2) {
				$this->assertInstanceOf(Tag::class, $tag1);
				$this->assertEquals($tag1->getClass(), \stdClass::class);
				return $tag2;
			})
		;
		$decorator2
			->expects($this->once())
			->method('decorateTag')
			->with($tag2)
			->willReturn($tag3)
		;

		$tagBuilder = new TagBuilder();
		$tagBuilder->addDecorator($decorator1);
		$tagBuilder->addDecorator($decorator2);

		$this->assertEquals($this->reflectionGetValue($tagBuilder, 'tags'), []);

		$this->assertEquals($tagBuilder->getTag(\stdClass::class), $tag3);

		$this->assertEquals($this->reflectionGetValue($tagBuilder, 'tags'), [ \stdClass::class => $tag3 ]);

		$this->assertEquals($tagBuilder->getTag(\stdClass::class), $tag3);

		$this->assertEquals($tagBuilder->getAllTags(), [ \stdClass::class => $tag3 ]);
	}
}