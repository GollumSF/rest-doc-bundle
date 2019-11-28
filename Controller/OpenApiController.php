<?php

namespace GollumSF\RestDocBundle\Controller;

use GollumSF\RestDocBundle\Generator\OpenApiGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;

class OpenApiController {
	
	/** @var OpenApiGeneratorInterface */
	private $openApiGenerator;
	
	public function __construct(OpenApiGeneratorInterface $openApiGenerator) {
		$this->openApiGenerator = $openApiGenerator;
	}

	public function __invoke() {
		$json = \json_encode($this->openApiGenerator->generate()['spec']);
		return new Response($json, Response::HTTP_OK, [ 'Content-Type' => 'application/json', 'Content-Length' => strlen($json) ]);
	}
}
