<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Handler;

class PhpTypeHandler implements HandlerInterface
{
	public function getType(string $class, string $targetName): string {
		return 'string';
	}
}