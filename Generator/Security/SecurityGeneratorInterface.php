<?php
namespace GollumSF\RestDocBundle\Generator\Security;

use GollumSF\RestDocBundle\Generator\Security\Handler\HandlerInterface;

interface SecurityGeneratorInterface {
	
	const HANDLER_TAG = 'gollumsf.rest_doc.generator.security.handler';

	public function addHandler(HandlerInterface $handler): void;
	
	public function generate(): SecurityCollection;
}