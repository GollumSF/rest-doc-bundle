<?php
namespace GollumSF\RestDocBundle\Generator\Security;

use GollumSF\RestDocBundle\Generator\Security\Handler\HandlerInterface;

class SecurityGenerator implements SecurityGeneratorInterface {

	/** @var HandlerInterface[] */
	private $handlers = [];

	public function addHandler(HandlerInterface $handler): void {
		$this->handlers[] = $handler;
	}
	
	public function generate(): SecurityCollection {
		$securityCollection = new SecurityCollection();
		foreach ($this->handlers as $handler) {
			$handler->generateSecurities($securityCollection);
		}
		return $securityCollection;
	}
}