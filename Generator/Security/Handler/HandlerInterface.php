<?php
namespace GollumSF\RestDocBundle\Generator\Security\Handler;

use GollumSF\RestDocBundle\Generator\Security\SecurityCollection;

interface HandlerInterface {
	public function generateSecurities(SecurityCollection $securityCollection): void;
}