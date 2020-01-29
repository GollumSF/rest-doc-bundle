<?php

namespace Test\GollumSF\RestDocBundle\Configuration;

use GollumSF\RestDocBundle\Configuration\ApiDocConfiguration;
use PHPUnit\Framework\TestCase;

class ApiDocConfigurationTest extends TestCase {

	public function testConstructor() {

		$apiDocConfiguration1 = new ApiDocConfiguration(
			'TITLE',
			'VERSION',
			'DESCRIPTION',
			[ 'DOMAIN.COM' ],
			'DOMAIN.COM',
			[ 'http' ],
			'http',
			[ 'EXTERNAL_DOC' ],
			[ 'SECURITY' ]
		);

		$this->assertEquals($apiDocConfiguration1->getTitle()          , 'TITLE');
		$this->assertEquals($apiDocConfiguration1->getVersion()        , 'VERSION');
		$this->assertEquals($apiDocConfiguration1->getDescription()    , 'DESCRIPTION');
		$this->assertEquals($apiDocConfiguration1->getHost()           , [ 'DOMAIN.COM' ]);
		$this->assertEquals($apiDocConfiguration1->getDefaultHost()    , 'DOMAIN.COM');
		$this->assertEquals($apiDocConfiguration1->getProtocol()       , [ 'http' ]);
		$this->assertEquals($apiDocConfiguration1->getDefaultProtocol(), 'http');
		$this->assertEquals($apiDocConfiguration1->getExternalDocs()   , [ 'EXTERNAL_DOC' ]);
		$this->assertEquals($apiDocConfiguration1->getSecurities()     , [ 'SECURITY' ]);


		$apiDocConfiguration2 = new ApiDocConfiguration(
			'TITLE',
			'VERSION',
			null,
			[ 'DOMAIN.COM' ],
			null,
			[ 'http' ],
			null,
			null,
			[ 'SECURITY' ]
		);

		$this->assertEquals($apiDocConfiguration2->getTitle()          , 'TITLE');
		$this->assertEquals($apiDocConfiguration2->getVersion()        , 'VERSION');
		$this->assertEquals($apiDocConfiguration2->getDescription()    , null);
		$this->assertEquals($apiDocConfiguration2->getHost()           , [ 'DOMAIN.COM' ]);
		$this->assertEquals($apiDocConfiguration2->getDefaultHost()    , null);
		$this->assertEquals($apiDocConfiguration2->getProtocol()       , [ 'http' ]);
		$this->assertEquals($apiDocConfiguration2->getDefaultProtocol(), null);
		$this->assertEquals($apiDocConfiguration2->getExternalDocs()   , null);
		$this->assertEquals($apiDocConfiguration2->getSecurities()     , [ 'SECURITY' ]);
	}
}