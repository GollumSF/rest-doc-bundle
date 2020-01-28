<?php

namespace GollumSF\RestDocBundle\Configuration;

interface ApiDocConfigurationInterface {

	public function getTitle(): string;

	public function getVersion(): string ;

	public function getDescription(): ?string;
	
	public function getExternalDocs(): ?array;
}