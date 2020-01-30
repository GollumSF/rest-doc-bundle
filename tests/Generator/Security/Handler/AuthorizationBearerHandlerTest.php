<?php
namespace Test\GollumSF\RestDocBundle\Generator\Security\Handler;

use GollumSF\RestDocBundle\Configuration\ApiDocConfigurationInterface;
use GollumSF\RestDocBundle\Generator\Security\Handler\AuthorizationBearerHandler;
use GollumSF\RestDocBundle\Generator\Security\SecurityCollection;
use PHPUnit\Framework\TestCase;

class AuthorizationBearerHandlerTest extends TestCase {
	
	public function providerGenerateSecurities() {
		return [
			[ [], [] ],
		
			[ [
				'name1' => [
					'type' => 'OTHER'
				]
			], [] ],

			[
				[
					'name1' => [
						'type' => AuthorizationBearerHandler::SECURITY_TAG,
						'name' => null,
						'scheme' => null,
						'defaultValue' => null,
					]
				], [
				'name1' => [
					'type' => 'apiKey',
					'in' => 'header',
					'description' => 'Value for the Authorization header',
					'name' => 'Authorization',
					"authenticationScheme" => 'BEARER',
					'defaultValue' => 'BEARER ',
				]
			]
			],

			[
				[
					'name1' => [
						'type' => 'OTHER'
					],
					'name2' => [
						'type' => AuthorizationBearerHandler::SECURITY_TAG,
						'name' => 'NAME',
						'scheme' => 'SCHEME',
						'defaultValue' => 'VALUE',
					]
				], [
				'name2' => [
					'type' => 'apiKey',
					'in' => 'header',
					'description' => 'Value for the Authorization header',
					'name' => 'NAME',
					"authenticationScheme" => 'SCHEME',
					'defaultValue' => 'SCHEME VALUE',
				]
			]
			],
		];
	}

	/**
	 * @dataProvider providerGenerateSecurities
	 */
	public function testGenerateSecurities($securities, $result) {
		
		$apiDocConfiguration = $this->getMockForAbstractClass(ApiDocConfigurationInterface::class);
		$apiDocConfiguration
			->expects($this->once())
			->method('getSecurities')
			->willReturn($securities)
		;

		$securityCollection = new SecurityCollection();
		
		$handler = new AuthorizationBearerHandler($apiDocConfiguration);
		$handler->generateSecurities($securityCollection);
		
		$this->assertEquals(
			$securityCollection->toArray(), $result
		);
	}
}