<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Handler;

interface HandlerInterface
{
	public function getType(string $class, string $targetName): string;
}