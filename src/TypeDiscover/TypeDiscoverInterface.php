<?php

namespace GollumSF\RestDocBundle\TypeDiscover;

use GollumSF\RestDocBundle\TypeDiscover\Handler\HandlerInterface;
use GollumSF\RestDocBundle\TypeDiscover\Models\TypeInterface;

interface TypeDiscoverInterface {

	const HANDLER_TAG = 'gollumsf.rest_doc.type_discover.handler';

	public function addHandler(HandlerInterface $handler): void;
	
	public function getType(string $class, string $targetName): TypeInterface;
	
}