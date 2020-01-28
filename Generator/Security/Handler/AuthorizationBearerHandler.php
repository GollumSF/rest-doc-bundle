<?php
namespace GollumSF\RestDocBundle\Generator\Security\Handler;

use GollumSF\RestDocBundle\Configuration\ApiDocConfigurationInterface;
use GollumSF\RestDocBundle\Generator\Security\SecurityCollection;

class AuthorizationBearerHandler implements HandlerInterface {
	
	const SECURITY_TAG = 'authorization_bearer';
	
	/** @var ApiDocConfigurationInterface */
	private $apiDocConfiguration;
	
	public function __construct(
		ApiDocConfigurationInterface $apiDocConfiguration
	) {
		$this->apiDocConfiguration = $apiDocConfiguration;
	}

	public function generateSecurities(SecurityCollection $securityCollection): void {

		foreach ($this->apiDocConfiguration->getSecurities() as $name => $security) {
			if ($security['type'] == self::SECURITY_TAG) {
				$scheme = $security['scheme'] ? $security['scheme'] : 'BEARER';
				$securityCollection->add($name, [
					'type' => 'apiKey',
					'in' => 'header',
					'description' => 'Value for the Authorization header',
					'name' => $security['name'] ? $security['name'] : 'Authorization',
					"authenticationScheme" => $scheme,
					'defaultValue' => $scheme.' '.($security['defaultValue'] ? $security['defaultValue'] : ''),
				]);
			}
		}
	}
}