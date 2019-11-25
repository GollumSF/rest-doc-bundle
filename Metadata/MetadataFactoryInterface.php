<?php

namespace GollumSF\RestDocBundle\Metadata;

use GollumSF\RestDocBundle\Metadata\Handler\HandlerInterface;

interface MetadataFactoryInterface
{
	const HANDLER_TAG = 'gollumsf.rest_doc.metadata_factory.handler';

	public function addHandler(HandlerInterface $handler);
	
	/**
	 * @return Metadata[]
	 */
	public function getMetadataCollection(): array;
}