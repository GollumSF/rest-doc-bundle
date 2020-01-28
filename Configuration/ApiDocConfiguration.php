<?php

namespace GollumSF\RestDocBundle\Configuration;

class ApiDocConfiguration implements ApiDocConfigurationInterface {

	/** @var string */
	private $title;

	/** @var string */
	private $version;

	/** @var string */
	private $description;

	/** @var array */
	private $externalDocs;
	
	public function __construct(
		string $title,
		string $version,
		?string $description,
		?array $externalDocs
	) {
		$this->title = $title;
		$this->version = $version;
		$this->description = $description;
		$this->externalDocs = $externalDocs;
	}

	public function getTitle(): string {
		return $this->title;
	}

	public function getVersion(): string {
		return $this->version;
	}

	public function getDescription(): ?string {
		return $this->description;
	}

	public function getExternalDocs(): ?array {
		return $this->externalDocs;
	}
}