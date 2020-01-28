<?php

namespace GollumSF\RestDocBundle\Configuration;

interface ApiDocConfigurationInterface {

	public function getTitle(): string;

	public function getVersion(): string ;

	public function getDescription(): ?string;

	public function getHost(): array;

	public function getDefaultHost(): ?string;

	public function getProtocol(): array;
	
	public function getDefaultProtocol(): ?string;
	
	public function getExternalDocs(): ?array;

}