<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Handler;

class DoctrineHandler implements HandlerInterface
{
	public function getType(string $class, string $targetName): string {
		return 'integer';
	}
}