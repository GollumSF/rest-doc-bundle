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
use GollumSF\RestDocBundle\Generator\ResponseProperties\ResponsePropertiesGeneratorInterface;
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
	
	/** @var ResponsePropertiesGeneratorInterface */
	private $responsePropertiesGenerator;
	
	/** @var RequestBodyGeneratorInterface */
	private $requestBodyGenerator;
	
	/** @var ApiDocConfigurationInterface */
	private $apiDocConfiguration;
	
	public function __construct(
		MetadataBuilderInterface             $metadataBuilderInterface,
		ModelBuilderInterface                $modelbuilder,
		TagBuilderInterface                  $tagbuilder,
		RequestStack                         $requestStack,
		ParametersGeneratorInterface         $parametersGenerator,
		ResponsePropertiesGeneratorInterface $responsePropertiesGenerator,
		RequestBodyGeneratorInterface        $requestBodyGenerator,
		ApiDocConfigurationInterface         $apiDocConfiguration
	) {
		$this->metadataBuilder             = $metadataBuilderInterface;
		$this->modelbuilder                = $modelbuilder;
		$this->tagbuilder                  = $tagbuilder;
		$this->requestStack                = $requestStack;
		$this->parametersGenerator         = $parametersGenerator;
		$this->responsePropertiesGenerator = $responsePropertiesGenerator;
		$this->requestBodyGenerator        = $requestBodyGenerator;
		$this->apiDocConfiguration         = $apiDocConfiguration;
	}
	
	public function generate(): array {

		$paths = $this->generatePaths();
		
		$request = $this->requestStack->getMasterRequest();
		
		$json = [
			'spec' => [
				'openapi' => self::OPEN_API_VERSION,
				'info' => $this->generateInfo(),
	
				'servers' => [
					[ 
						'url' => '{protocol}://'.$request->getHost().$this->getBasePath(),
						'variables' => [
							'protocol' => [ 'enum' => [ $request->getScheme() ], 'default' => $request->getScheme() ]	
						]
					],
				],
	
				'tags' => array_values(array_map(function (Tag $tag) { return $tag->toJson(); }, $this->tagbuilder->getAllTags())),
				'paths' => $paths,
				'components' => [
					'securitySchemes' => [
						'ApiKeyHeader' => [
							'type' => 'apiKey',
							'in' => 'header',
							'description' => 'Value for the Authorization header',
							'name' => 'Authorization',
							"authenticationScheme" => "Bearer"
						],
						'ApiKeyQuery' => [
							'type' => 'apiKey',
							'in' => 'query',
							'description' => 'Value for the token query',
							'name' => 'token',
						]
					],
					'schemas' => array_map(function (ObjectType $model) { return $model->toJsonRef(); }, $this->modelbuilder->getAllModels())
				],
				'security' => [
					[ 'ApiKeyHeader' => [] ],
					[ 'ApiKeyQuery' => [] ],
				],
			],
			'security' => [
				[ 'defaultValue' => 'BEARER TOKEN_DEV' ],
				[ 'defaultValue' => 'TOKEN_DEV' ],
			]
		];

		if ($this->generateExternalDocs()) {
			$json['spec']['externalDocs'] = $this->generateExternalDocs();
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
		if ($path[strlen($path) - 1] === '/') {
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

			$tag = $this->tagbuilder->gettag($entity);

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
//					'summary'=> 'Creates a Book resource.',
					'parameters' => $parameters,
					'responses' => $responses,
				];
				if ($this->hasRequestBody($metadata, $method)) {
					$path['requestBody'] = $this->generateRequestBody($metadata, $method);
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

			$responseProperties = $this->responsePropertiesGenerator->generate($metadata, $method)->toArray();

			$responses[$annoSerialize->code] = [
//				'description' => 'successful operation',
				'content' => [
					'application/json' => [
						'schema' => [
							'type' => 'object',
							'properties' => $responseProperties
						]
					]
				]
			];
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
		if ($this->apiDocConfiguration->getDescription()) {
			$infos['description'] = $this->apiDocConfiguration->getDescription();
		}
		return $infos;
	}

	protected function generateExternalDocs(): ?array {
		$externalDocs = null;
		if ($this->apiDocConfiguration->getExternalDocs()) {
			$externalDocs = [
				'url' => $this->apiDocConfiguration->getExternalDocs()['url'],
			];
			if ($this->apiDocConfiguration->getExternalDocs()['description']) {
				$externalDocs['description'] = $this->apiDocConfiguration->getExternalDocs()['description'];
			}
		}
		return $externalDocs;
	}
}