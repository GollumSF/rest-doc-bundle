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
		OpenApiGeneratorInterface $openApiGenerator
	) {
		$this->openApiGenerator = $openApiGenerator;
	}

	public function setTwig(Environment $twig): self {
		$this->twig = $twig;
		return $this;
	}

	public function __invoke() {
		if (!$this->twig) {
			throw new \LogicException(sprintf('%s service not declared. install Symfony Twig', Environment::class));
		}
		return new Response(
			$this->twig->render('@GollumSFRestDoc/SwaggerUI/index.html.twig', [
				'swaggerData' => $this->openApiGenerator->generate()
			]),
			Response::HTTP_OK,
			[ 'Content-Type' => 'text/html' ]
		);
	}
}
