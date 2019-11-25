<?php

namespace GollumSF\RestDocBundle\TypeDiscover\Handler;

class AnnotationHandler implements HandlerInterface
{
	public function getType(string $class, string $targetName): string {
		return 'string';
	}
}