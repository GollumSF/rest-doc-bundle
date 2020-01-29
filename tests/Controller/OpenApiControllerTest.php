<?php

namespace  Test\GollumSF\RestDocBundle\Controller;

use GollumSF\RestDocBundle\Controller\OpenApiController;
use GollumSF\RestDocBundle\Generator\OpenApiGeneratorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class OpenApiControllerTest extends TestCase {
	
//	/** @var OpenApiGeneratorInterface */
//	private $openApiGenerator;
//	
//	public function __construct(OpenApiGeneratorInterface $openApiGenerator) {
//		$this->openApiGenerator = $openApiGenerator;
//	}

	public function testInvoke() {

		$openApiGenerator = $this->getMockBuilder(OpenApiGeneratorInterface::class)->getMockForAbstractClass();
		$openApiGenerator
			->expects($this->once())
			->method('generate')
			->willReturn([ 'key' => 'value' ])
		;
		
		$openApiController = new OpenApiController($openApiGenerator);

		$response = $openApiController->__invoke();

		$this->assertEquals($response->getContent(), '{"key":"value"}');
		$this->assertEquals($response->getStatusCode(), Response::HTTP_OK);
		$this->assertEquals($response->headers->get('content-type'), 'application/json');
		$this->assertEquals($response->headers->get('content-length'), 15);
	}
}
