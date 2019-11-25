<?php

namespace GollumSF\RestDocBundle\TypeDiscover;

use GollumSF\RestDocBundle\TypeDiscover\Handler\HandlerInterface;

class TypeDiscover implements TypeDiscoverInterface {
	
	/** @var HandlerInterface[] */
	private $handlers = [];
	
	public function addHandler(HandlerInterface $handler): void {
		$this->handlers[] = $handler;
	}
	
	public function getType(string $class, string $targetName): string
	{
		foreach ($this->handlers as $handler) {
			$type = $handler->getType($class, $targetName);
			if ($type) {
				return $type;
			}
		}
		return 'mixed';
	}
}