<?php

namespace GollumSF\RestDocBundle\Generator;

use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\MetadataBuilderInterface;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\Builder\TagBuilder\Tag;
use GollumSF\RestDocBundle\Builder\TagBuilder\TagBuilderInterface;
use GollumSF\RestDocBundle\Configuration\ApiDocConfigurationInterface;
use GollumSF\RestDocBundle\Generator\Parameters\ParametersGeneratorInterface;
use GollumSF\RestDocBundle\Generator\RequestBody\RequestBodyGeneratorInterface;
use GollumSF\RestDocBundle\Generator\ResponseBody\ResponseBodyGeneratorInterface;
use GollumSF\RestDocBundle\Generator\Security\SecurityGeneratorInterface;
use GollumSF\RestDocBundle\TypeDiscover\Models\ObjectType;
use Symfony\Component\HttpFoundation\RequestStack;

class OpenApiGenerator implements OpenApiGeneratorInterface {

	const OPEN_API_VERSION = '3.0.2';
	
	/** @var MetadataBuilderInterface */
	private $metadataBuilder;

	/** @var ModelbuilderInterface */
	private $modelbuilder;
	
	/** @var TagbuilderInterface */
	private $tagbuilder;
	
	/** @var RequestStack */
	private $requestStack;
	
	/** @var ParametersGeneratorInterface */
	private $parametersGenerator;
	
	/** @var ResponseBodyGeneratorInterface */
	private $responseBodyGenerator;
	
	/** @var RequestBodyGeneratorInterface */
	private $requestBodyGenerator;
	
	/** @var SecurityGeneratorInterface */
	private $securityGenerator;
	
	/** @var ApiDocConfigurationInterface */
	private $apiDocConfiguration;
	
	public function __construct(
		MetadataBuilderInterface       $metadataBuilderInterface,
		ModelBuilderInterface          $modelbuilder,
		TagBuilderInterface            $tagbuilder,
		RequestStack                   $requestStack,
		ParametersGeneratorInterface   $parametersGenerator,
		ResponseBodyGeneratorInterface $responseBodyGenerator,
		RequestBodyGeneratorInterface  $requestBodyGenerator,
		SecurityGeneratorInterface     $securityGenerator,
		ApiDocConfigurationInterface   $apiDocConfiguration
	) {
		$this->metadataBuilder       = $metadataBuilderInterface;
		$this->modelbuilder          = $modelbuilder;
		$this->tagbuilder            = $tagbuilder;
		$this->requestStack          = $requestStack;
		$this->parametersGenerator   = $parametersGenerator;
		$this->responseBodyGenerator = $responseBodyGenerator;
		$this->requestBodyGenerator  = $requestBodyGenerator;
		$this->securityGenerator     = $securityGenerator;
		$this->apiDocConfiguration   = $apiDocConfiguration;
	}
	
	public function generate(): array {

		$security = $this->generateSecurity();
		
		$json = [
			'openapi' => self::OPEN_API_VERSION,
			'info'    => $this->generateInfo(),
			'servers' => $this->generateServers(),
			'paths'   => $this->generatePaths(),
			'tags'    => array_values(array_map(function (Tag $tag) { return $tag->toJson(); }, $this->tagbuilder->getAllTags())),
			'components' => [
				'schemas' => array_map(function (ObjectType $model) { return $model->toJsonRef(); }, $this->modelbuilder->getAllModels()),
				'securitySchemes' => $security
			],
			'security' => array_map(function ($value) { return [ $value => [] ]; }, array_keys($security))
		];

		$externalDocs = $this->generateExternalDocs();
		if ($externalDocs) {
			$json['externalDocs'] = $externalDocs;
		}
		
		return $json;
	}

	protected function getBasePath(): string {
		$path = null;
		
		foreach ($this->metadataBuilder->getMetadataCollection() as $metadata) {
			
			$route = $metadata->getRoute();
			$url = $route->getPath();
			if ($path === null) {
				$path = $url;
				continue;
			}
			$newPath = '';
			for ($i = 0; $i < strlen($url); $i++) {
				if ($i >= strlen($path) || $url[$i] !== $path[$i]) {
					break;
				}
				$newPath .= $path[$i];
			}
			$path = $newPath;
		}
		if (null == $path) {
			$path = '';
		}
		if ($path && $path[strlen($path) - 1] === '/') {
			$path = substr($path, 0, -1);
		}
		
		return $path;
	}
	
	protected function generatePaths(): array {
		
		$paths = [];

		$basePath = $this->getBasePath();
		
		foreach ($this->metadataBuilder->getMetadataCollection() as $metadata) {

			$route = $metadata->getRoute();
			$entity = $metadata->getEntity();

			$tag = $this->tagbuilder->getTag($entity);

			$url = $route->getPath();
			$methods = $route->getMethods();
			$url = substr($url, strlen($basePath));


			if (!isset($paths[$url])) {
				$paths[$url] = [];
			}

			foreach ($methods as $method) {

				$parameters = $this->generateParameters($url, $metadata, $method);
				$responses = $this->generateResponse($metadata, $method);

				$path = [
					'tags' => [$tag->getClass()],
					'parameters' => $parameters,
					'responses' => $responses,
				];
				if ($this->hasRequestBody($metadata, $method)) {
					$path['requestBody'] = $this->generateRequestBody($metadata, $method);
				}
				if ($summary = $metadata->getSummary()) {
					$path['summary'] = $summary;
				}

				$paths[$url][strtolower($method)] = $path;
			}

		}
		return $paths;
	}
	
	protected function generateParameters($url, Metadata $metadata, string $method): array {
		return $this->parametersGenerator->generate($url, $metadata, $method)->toArray();
	}

	protected function generateResponse(Metadata $metadata, string $method): array {
		
		$annoSerialize = $metadata->getSerialize();
		
		$responses = [];
		if ($annoSerialize) {

			$resposneJson = [
//				'description' => 'successful operation',
			];
			if ($this->responseBodyGenerator->hasResponseBody($metadata, $method)) {
				$responseProperties = $this->responseBodyGenerator->generateProperties($metadata, $method)->toArray();
				$resposneJson['content'] = [
					'application/json' => [
						'schema' => [
							'type' => 'object',
							'properties' => $responseProperties
						]
					]
				];
			}
			$responses[$annoSerialize->getCode()] = $resposneJson;
		}
		return $responses;
	}

	protected function hasRequestBody(Metadata $metadata, string $method): bool {
		return $this->requestBodyGenerator->hasRequestBody($metadata, $method);
	}
	
	protected function generateRequestBody(Metadata $metadata, string $method): array {
		return [
			'content' => [
				'application/json' => [
					'schema' => [
						'type' => 'object',
						'properties' => $this->requestBodyGenerator->generateProperties($metadata, $method)->toArray()
					]
				]
			]
		];
	}

	protected function generateInfo(): array {
		$infos = [
			'title' => $this->apiDocConfiguration->getTitle(),
			'version' => $this->apiDocConfiguration->getVersion(),
		];
		if ($description = $this->apiDocConfiguration->getDescription()) {
			$infos['description'] = $description;
		}
		return $infos;
	}

	protected function generateExternalDocs(): ?array {
		$externalDocs = null;
		if ($docs = $this->apiDocConfiguration->getExternalDocs()) {
			$externalDocs = [
				'url' => $docs['url'],
			];
			if (isset($docs['description']) && $docs['description']) {
				$externalDocs['description'] = $docs['description'];
			}
		}
		return $externalDocs;
	}

	protected function generateServers(): array
	{
		$request = $this->requestStack->getMasterRequest();

		$host = $this->apiDocConfiguration->getHost() ? $this->apiDocConfiguration->getHost() : [ $request->getHost() ];
		$defaultHost = $this->apiDocConfiguration->getDefaultHost() ? $this->apiDocConfiguration->getDefaultHost() : $host[0];

		$protocols = $this->apiDocConfiguration->getProtocol() ? $this->apiDocConfiguration->getProtocol() : [ $request->getScheme() ];
		$defaultProtocol = $this->apiDocConfiguration->getDefaultProtocol() ? $this->apiDocConfiguration->getDefaultProtocol() : $protocols[0];

		return [
			[
				'url' => '{protocol}://{base_uri}',
				'variables' => [
					'base_uri' => ['enum' => array_map(function ($h) {
						return $h.$this->getBasePath();
					}, $host), 'default' => $defaultHost.$this->getBasePath() ],
					'protocol' => ['enum' => $protocols, 'default' => $defaultProtocol ]
				]
			]
		];
	}

	protected function generateSecurity(): array {
		return $this->securityGenerator->generate()->toArray();
	}
}