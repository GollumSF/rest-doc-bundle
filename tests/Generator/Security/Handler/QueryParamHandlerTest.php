<?php
namespace Test\GollumSF\RestDocBundle\Generator\Security\Handler;

use GollumSF\RestDocBundle\Configuration\ApiDocConfigurationInterface;
use GollumSF\RestDocBundle\Generator\Security\Handler\QueryParamHandler;
use GollumSF\RestDocBundle\Generator\Security\SecurityCollection;
use PHPUnit\Framework\TestCase;

class QueryParamHandlerTest extends TestCase {
	
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
						'type' => QueryParamHandler::SECURITY_TAG,
						'name' => null,
						'scheme' => null,
						'defaultValue' => null,
					]
				],
				[
					'name1' => [
						'type' => 'apiKey',
						'in' => 'query',
						'description' => 'Value for the token query',
						'name' => 'token',
						'defaultValue' => '',
					]
				]
			],

			[
				[
					'name1' => [
						'type' => 'OTHER'
					],
					'name2' => [
						'type' => QueryParamHandler::SECURITY_TAG,
						'name' => 'NAME',
						'defaultValue' => 'VALUE',
					]
				],
				[
					'name2' => [
						'type' => 'apiKey',
						'in' => 'query',
						'description' => 'Value for the token query',
						'name' => 'NAME',
						'defaultValue' => 'VALUE',
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
		
		$handler = new QueryParamHandler($apiDocConfiguration);
		$handler->generateSecurities($securityCollection);
		
		$this->assertEquals(
			$securityCollection->toArray(), $result
		);
	}
}