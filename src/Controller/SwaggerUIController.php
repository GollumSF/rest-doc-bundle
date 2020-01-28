<?php

namespace GollumSF\RestDocBundle\Controller;

use GollumSF\RestDocBundle\Generator\OpenApiGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class SwaggerUIController {
	
	/** @var Environment */
	private $twig;

	/** @var OpenApiGeneratorInterface */
	private $openApiGenerator;
	
	public function __construct(
		Environment $twig,
		OpenApiGeneratorInterface $openApiGenerator
	) {
		$this->twig = $twig;
		$this->openApiGenerator = $openApiGenerator;
	}

	public function __invoke() {
		return new Response(
			$this->twig->render('@GollumSFRestDoc/SwaggerUI/index.html.twig', [
				'swaggerData' => $this->openApiGenerator->generate()
			]),
			Response::HTTP_OK,
			[ 'Content-Type' => 'text/html' ]
		);
	}
}
