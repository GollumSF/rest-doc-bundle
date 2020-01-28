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
	
	/** @var array */
	private $host;
	
	/** @var string */
	private $defaultHost;
	
	/** @var array  */
	private $protocol;
	
	/** @var string */
	private $defaultProtocol;
	
	/** @var string */
	private $securities;
	
	public function __construct(
		string $title,
		string $version,
		?string $description,
		array $host,
		?string $defaultHost,
		array $protocol,
		?string $defaultProtocol,
		?array $externalDocs,
		array $securities
	) {
		$this->title = $title;
		$this->version = $version;
		$this->description = $description;
		$this->host = $host;
		$this->protocol = $protocol;
		$this->defaultHost = $defaultHost;
		$this->defaultProtocol = $defaultProtocol;
		$this->externalDocs = $externalDocs;
		$this->securities = $securities;
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

	public function getHost(): array {
		return $this->host;
	}

	public function getDefaultHost(): ?string {
		return $this->defaultHost;
	}

	public function getProtocol(): array {
		return $this->protocol;
	}

	public function getDefaultProtocol(): ?string {
		return $this->defaultProtocol;
	}

	public function getExternalDocs(): ?array {
		return $this->externalDocs;
	}
	
	public function getSecurities(): array {
		return $this->securities;
	}
}