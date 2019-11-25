<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Handler;

use GollumSF\RestDocBundle\TypeDiscover\Models\TypeInterface;

class PropertyInfosHandler implements HandlerInterface
{
	public function getType(string $class, string $targetName): ?TypeInterface {
		return null;
	}
}