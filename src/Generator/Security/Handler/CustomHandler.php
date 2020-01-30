<?php
namespace GollumSF\RestDocBundle\Generator\Security\Handler;

use GollumSF\RestDocBundle\Configuration\ApiDocConfigurationInterface;
use GollumSF\RestDocBundle\Generator\Security\SecurityCollection;

class CustomHandler implements HandlerInterface {
	
	const SECURITY_TAG = 'custom';
	
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
				$securityCollection->add($name, array_merge([
					'defaultValue' => $security['defaultValue'] ? $security['defaultValue'] : ''
				], $security['data']));
			}
		}
	}
}