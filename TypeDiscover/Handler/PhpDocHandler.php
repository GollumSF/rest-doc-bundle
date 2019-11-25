<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Handler;

class PhpDocHandler implements HandlerInterface
{
	public function getType(string $class, string $targetName): string {
		return 'string';
	}
}