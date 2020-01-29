<?php

namespace Test\GollumSF\RestDocBundle\Builder\TagBuilder\Decorator;

use Doctrine\Common\Annotations\Reader;
use GollumSF\RestDocBundle\Annotation\ApiEntity;
use GollumSF\RestDocBundle\Builder\TagBuilder\Decorator\AnnotationDecorator;
use GollumSF\RestDocBundle\Builder\TagBuilder\Tag;
use PHPUnit\Framework\TestCase;

class AnnotationDecoratorTest extends TestCase {
	
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

		$reader = $this->getMockBuilder(Reader::class)->disableOriginalConstructor()->getMock();

		$tag = new Tag(\stdClass::class);
		
		$reader
			->expects($this->once())
			->method('getClassAnnotation')
			->willReturnCallback(function ($rClass, $annoClass) use ($annoApiEntity) {
				$this->assertInstanceOf(\ReflectionClass::class, $rClass);
				$this->assertEquals($rClass->getName(), \stdClass::class);
				$this->assertEquals($annoClass, ApiEntity::class);
				return $annoApiEntity;
			});
		
		$annotationDecorator = new AnnotationDecorator($reader);
		
		$this->assertEquals($annotationDecorator->decorateTag($tag), $tag);

		$this->assertEquals($tag->getUrl(), $url);
		$this->assertEquals($tag->getDescription(),$description);
		$this->assertEquals($tag->getDocDescription(), $docDescription);
	}
}