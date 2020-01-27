<?php

namespace GollumSF\RestDocBundle\Builder\MetadataBuilder;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Handler\HandlerInterface;

interface MetadataBuilderInterface
{
	const HANDLER_TAG = 'gollumsf.rest_doc.metadata_builder.handler';

	public function addHandler(HandlerInterface $handler): void;
	
	/**
	 * @return Metadata[]
	 */
	public function getMetadataCollection(): array;
}