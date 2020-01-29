<?php

namespace Test\GollumSF\RestDocBundle\Builder\TagBuilder;

use GollumSF\RestDocBundle\Builder\TagBuilder\Tag;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase {

	public function testConstructor() {
		$tag = new Tag(\stdClass::class);
		$this->assertEquals($tag->getClass(), \stdClass::class);
	}

	public function testSetter() {
		$tag = new Tag(\stdClass::class);
		$tag
			->setUrl('URL')
			->setDescription('DESCRIPTION')
			->setDocDescription('DOC_DESCRIPTION')
		;
		$this->assertEquals($tag->getUrl(), 'URL');
		$this->assertEquals($tag->getDescription(),'DESCRIPTION');
		$this->assertEquals($tag->getDocDescription(), 'DOC_DESCRIPTION');
	}
	
	public function providerToJson() {
		return [
			[ null, null, null, [
				'name' => \stdClass::class,
			] ],

			[ 'DESCRIPTION', null, null, [
				'name' => \stdClass::class,
				'description' => 'DESCRIPTION',
			] ],

			[ 'DESCRIPTION', null, 'DOC_DESCRIPTION', [
				'name' => \stdClass::class,
				'description' => 'DESCRIPTION',
				'externalDocs' => [
					'description' => 'DOC_DESCRIPTION',
				],
			] ],
			
			[ 'DESCRIPTION', 'URL', null, [
				'name' => \stdClass::class,
				'description' => 'DESCRIPTION',
				'externalDocs' => [
					'url' => 'URL',
				],
			] ],

			[ null, 'URL', 'DOC_DESCRIPTION', [
				'name' => \stdClass::class,
				'externalDocs' => [
					'url' => 'URL',
					'description' => 'DOC_DESCRIPTION',
				],
			] ],

			[ 'DESCRIPTION', 'URL', 'DOC_DESCRIPTION', [
				'name' => \stdClass::class,
				'description' => 'DESCRIPTION',
				'externalDocs' => [
					'url' => 'URL',
					'description' => 'DOC_DESCRIPTION',
				],
			] ],
		];
	}
	
	/**
	 * @dataProvider providerToJson
	 */
	public function testToJson($description, $url, $docDescription, $result) {
		$tag = new Tag(\stdClass::class);
		$tag
			->setDescription($description)
			->setUrl($url)
			->setDocDescription($docDescription)
		;
		$this->assertEquals($tag->toJson(), $result);
	}
	
//	/** @var string */
//	private $class;
//
//	/** @var string */
//	public $description;
//
//	/** @var string */
//	public $url;
//
//	/** @var string */
//	public $docDescription;
//
//	public function __construct(string $class)
//	{
//		$this->class = $class;
//	}
//	
//	/////////////
//	// Getters //
//	/////////////
//
//	public function getClass(): string
//	{
//		return $this->class;
//	}
//
//	public function getDescription(): ?string
//	{
//		return $this->description;
//	}
//
//	public function getUrl(): ?string
//	{
//		return $this->url;
//	}
//
//	public function getDocDescription(): ?string
//	{
//		return $this->docDescription;
//	}
//
//	/////////////
//	// Setters //
//	/////////////
//
//	public function setDescription(?string $description): self
//	{
//		$this->description = $description;
//		return $this;
//	}
//
//	public function setUrl(?string $url): self
//	{
//		$this->url = $url;
//		return $this;
//	}
//
//	public function setDocDescription(?string $docDescription): self
//	{
//		$this->docDescription = $docDescription;
//		return $this;
//	}
//	
//
//	////////////
//	// Others //
//	////////////
//
//	public function toJson(): array {
//		
//		$json = [
//			'name' => $this->getClass(),
//		];
//		if ($this->getDescription()) {
//			$json['description'] = $this->getDescription();
//		}
//		if ($this->getUrl()) {
//			if (!isset($json['externalDocs'])) {
//				$json['externalDocs'] = [];
//			}
//			$json['externalDocs']['url'] = $this->getUrl();
//		}
//		if ($this->getDocDescription()) {
//			if (!isset($json['externalDocs'])) {
//				$json['externalDocs'] = [];
//			}
//			$json['externalDocs']['description'] = $this->getDocDescription();
//		}
//		return $json;
//	}
}