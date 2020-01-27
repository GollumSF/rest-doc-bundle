<?php

namespace GollumSF\RestDocBundle\Builder\TagBuilder;

class Tag
{
	/** @var string */
	private $class;

	/** @var string */
	public $description;

	/** @var string */
	public $url;

	/** @var string */
	public $docDescription;

	public function __construct(string $class)
	{
		$this->class = $class;
	}
	
	/////////////
	// Getters //
	/////////////

	public function getClass(): string
	{
		return $this->class;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function getUrl(): ?string
	{
		return $this->url;
	}

	public function getDocDescription(): ?string
	{
		return $this->docDescription;
	}

	/////////////
	// Setters //
	/////////////

	public function setDescription(?string $description): self
	{
		$this->description = $description;
		return $this;
	}

	public function setUrl(?string $url): self
	{
		$this->url = $url;
		return $this;
	}

	public function setDocDescription(?string $docDescription): self
	{
		$this->docDescription = $docDescription;
		return $this;
	}
	

	////////////
	// Others //
	////////////

	public function toJson(): array {
		
		$json = [
			'name' => $this->getClass(),
		];
		if ($this->getDescription()) {
			$json['description'] = $this->getDescription();
		}
		if ($this->getUrl()) {
			if (!isset($json['externalDocs'])) {
				$json['externalDocs'] = [];
			}
			$json['externalDocs']['url'] = $this->getUrl();
		}
		if ($this->getDocDescription()) {
			if (!isset($json['externalDocs'])) {
				$json['externalDocs'] = [];
			}
			$json['externalDocs']['description'] = $this->getDocDescription();
		}
		return $json;
	}
}