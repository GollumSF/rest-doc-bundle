<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Handler;

use GollumSF\RestDocBundle\TypeDiscover\Models\TypeInterface;

interface HandlerInterface
{
	public function getType(string $class, string $targetName): ?TypeInterface;
}