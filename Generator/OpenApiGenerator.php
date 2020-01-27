<?php

namespace GollumSF\RestDocBundle\Generator;

use GollumSF\RestBundle\Annotation\Serialize;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\MetadataBuilderInterface;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\Builder\TagBuilder\Tag;
use GollumSF\RestDocBundle\Builder\TagBuilder\TagBuilderInterface;
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
	
	public function __construct(
		MetadataBuilderInterface             $metadataBuilderInterface,
		ModelBuilderInterface                $modelbuilder,
		TagBuilderInterface                  $tagbuilder,
		RequestStack                         $requestStack,
		ParametersGeneratorInterface         $parametersGenerator,
		ResponsePropertiesGeneratorInterface $responsePropertiesGenerator,
		RequestBodyGeneratorInterface       $requestBodyGenerator
	) {
		$this->metadataBuilder             = $metadataBuilderInterface;
		$this->modelbuilder                = $modelbuilder;
		$this->tagbuilder                  = $tagbuilder;
		$this->requestStack                = $requestStack;
		$this->parametersGenerator         = $parametersGenerator;
		$this->responsePropertiesGenerator = $responsePropertiesGenerator;
		$this->requestBodyGenerator        = $requestBodyGenerator;
	}

	
	public function generate(): array {

		$paths = $this->generatePaths();
		
		$request = $this->requestStack->getMasterRequest();
		
		return [
			'spec' => [
				'openapi' => self::OPEN_API_VERSION,
				'info' => [
					'description' => 'description API',
					'version' => '1.0.0',
					'title' => 'Swagger Title'
				],
				'externalDocs' => [
					'description' => 'Descript doc externe',
					'url' => 'https://google.fr'
				],
	
				'servers' => [
					[ 
						'url' => '{protocol}://'.$request->getHost().'/api',
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
	}

	protected function generatePaths(): array {
		
		$paths = [];

		foreach ($this->metadataBuilder->getMetadataCollection() as $metadata) {

			/** @var Serialize $annoSerialize */
			$route = $metadata->getRoute();
			$entity = $metadata->getEntity();

			$tag = $this->tagbuilder->gettag($entity);

			$url = $route->getPath();
			$methods = $route->getMethods();
			$url = substr($url, strlen('/api'));


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

	protected function generateResponse(Metadata $metadata, string $method): array
	{
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
}