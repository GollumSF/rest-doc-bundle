<?php
namespace Test\GollumSF\RestDocBundle\Generator\Security\Handler;

use GollumSF\RestDocBundle\Configuration\ApiDocConfigurationInterface;
use GollumSF\RestDocBundle\Generator\Security\Handler\CustomHandler;
use GollumSF\RestDocBundle\Generator\Security\SecurityCollection;
use PHPUnit\Framework\TestCase;

class CustomHandlerTest extends TestCase {
	
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
						'type' => CustomHandler::SECURITY_TAG,
						'data' => [ 'KEY' => 'VALUE' ],
						'defaultValue' => null,
					]
				],
				[
					'name1' => [
						'KEY' => 'VALUE',
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
						'type' => CustomHandler::SECURITY_TAG,
						'data' => [ 'KEY' => 'VALUE' ],
						'defaultValue' => 'VALUE',
					]
				],
				[
					'name2' => [
						'KEY' => 'VALUE',
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
		
		$handler = new CustomHandler($apiDocConfiguration);
		$handler->generateSecurities($securityCollection);
		
		$this->assertEquals(
			$securityCollection->toArray(), $result
		);
	}
}