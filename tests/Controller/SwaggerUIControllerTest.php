<?php

namespace  Test\GollumSF\RestDocBundle\Controller;

use GollumSF\RestDocBundle\Controller\SwaggerUIController;
use GollumSF\RestDocBundle\Generator\OpenApiGeneratorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class SwaggerUIControllerTest extends TestCase {
	
	public function testInvoke() {

		$environment = $this->getMockBuilder(Environment::class)->disableOriginalConstructor()->getMock();
		$environment
			->expects($this->once())
			->method('render')
			->with('@GollumSFRestDoc/SwaggerUI/index.html.twig', [ 'swaggerData' => [ 'key' => 'value' ]])
			->willReturn('<html><body></body></html>')
		;
		
		$openApiGenerator = $this->getMockBuilder(OpenApiGeneratorInterface::class)->getMockForAbstractClass();
		$openApiGenerator
			->expects($this->once())
			->method('generate')
			->willReturn([ 'key' => 'value' ])
		;
		
		$swaggerUIController = new SwaggerUIController(
			$environment,
			$openApiGenerator
		);

		$response = $swaggerUIController->__invoke();

		$this->assertEquals($response->getContent(), '<html><body></body></html>');
		$this->assertEquals($response->getStatusCode(), Response::HTTP_OK);
		$this->assertEquals($response->headers->get('content-type'), 'text/html');
	}
}
