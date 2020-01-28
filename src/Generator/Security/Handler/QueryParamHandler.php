<?php
namespace GollumSF\RestDocBundle\Generator\Security\Handler;

use GollumSF\RestDocBundle\Configuration\ApiDocConfigurationInterface;
use GollumSF\RestDocBundle\Generator\Security\SecurityCollection;

class QueryParamHandler implements HandlerInterface {
	
	const SECURITY_TAG = 'query_param';
	
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
				$securityCollection->add($name, [
					'type' => 'apiKey',
					'in' => 'query',
					'description' => 'Value for the token query',
					'name' => $security['name'] ? $security['name'] : 'token',
					'defaultValue' => $security['defaultValue'] ? $security['defaultValue'] : '',
				]);
			}
		}
	}
}