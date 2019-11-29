<?php

namespace GollumSF\RestDocBundle\Generator;

use Doctrine\Common\Annotations\Reader;
use GollumSF\RestBundle\Annotation\Serialize;
use GollumSF\RestBundle\Annotation\Unserialize;
use GollumSF\RestDocBundle\Generator\MetadataBuilder\MetadataBuilderInterface;
use GollumSF\RestDocBundle\Generator\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\Generator\TagBuilder\Tag;
use GollumSF\RestDocBundle\Generator\TagBuilder\TagBuilderInterface;
use GollumSF\RestDocBundle\TypeDiscover\Models\ObjectType;
use Symfony\Component\HttpFoundation\RequestStack;

class OpenApiGenerator implements OpenApiGeneratorInterface {

	/** @var MetadataBuilderInterface */
	private $metadataBuilder;

	/** @var ModelbuilderInterface */
	private $modelbuilder;
	
	/** @var TagbuilderInterface */
	private $tagbuilder;
	
	/** @var RequestStack */
	private $requestStack;
	
	public function __construct(
		MetadataBuilderInterface      $metadataBuilderInterface,
		ModelBuilderInterface         $modelbuilder,
		TagBuilderInterface           $tagbuilder,
		RequestStack                  $requestStack
	) {
		$this->metadataBuilder = $metadataBuilderInterface;
		$this->modelbuilder    = $modelbuilder;
		$this->tagbuilder      = $tagbuilder;
		$this->requestStack    = $requestStack;
	}

	
	public function generate(): array
	{

		$paths = [];
		
		foreach ($this->metadataBuilder->getMetadataCollection() as $metadata) {

			/** @var Serialize $annoSerialize */
			$route           = $metadata->getRoute();
			$entity          = $metadata->getEntity();
			$isCollection    = $metadata->isCollection();
			$annoSerialize   = $metadata->getSerialize();
			
			$tag = $this->tagbuilder->gettag($entity);
			$model = $this->modelbuilder->getModel($entity);
			
			$url = $route->getPath();
			$methods = $route->getMethods();
			$url = substr($url, strlen('/api'));

			$requestBody = [];
			$parameters = [];
			
			if (!isset($paths[$url])) {
				$paths[$url] = [];
			}


			preg_match_all('/\{([a-zA-Z-9_]+)\}/', $url, $match);
			foreach ($match[1] as $key) {
				$parameters[] = [
					'name' => $key,
					'in' => 'path',
//						'required' => false,
//						'type' => "string"
				];
			}
			
			if ($isCollection) {
				$parameters[] = [
					'name' => 'limit',
					'in' => 'query',
					'required' => false,
					'type' => 'integer',
					'minimum' => 1,
				];
				$parameters[] = [
					'name' => 'page',
					'in' => 'query',
					'required' => false,
					'type' => 'integer',
				];
				$parameters[] = [
					'name' => 'order',
					'in' => 'query',
					'required' => false,
					'type' => 'string',
				];
				$parameters[] = [
					'name' => 'direction',
					'in' => 'query',
					'required' => false,
					'type' => 'string',
					'enum' => [
						"asc",
						"desc",
					]
				];
			}
			
			foreach ($metadata->getRequestProperties() as $name => $parameter) {
				$parameters[] = array_merge([ 'name' => $name ], $parameter);
			}
			
			foreach ($methods as $method) {

				$hasRquestBody = false;
				$requestProperties = [];
				if ($metadata->getUnserializeGroups()) {
					$hasRquestBody = true;
					$groups = array_merge([strtolower($method)], $metadata->getUnserializeGroups());
					$groups = array_unique($groups);

					foreach ($model->getProperties() as $property) {
						if (count(array_intersect($property->getGroups(), $groups))) {
							$requestProperties[$property->getSerializeName()] = $property->getType()->toJson($groups);
						}
					}
				}
				if ($metadata->getRequestBodyProperties()) {
					$hasRquestBody = true;
					$requestProperties = array_merge($requestProperties, $metadata->getRequestBodyProperties());
				}
				
				if ($hasRquestBody) {

					$requestBody['content'] = [
						'application/json' => [
							'schema' => [
								'type' => 'object',
								'properties' => $requestProperties
							]
						]
					];
					
				}

				$responses = [];
				if ($annoSerialize) {

					$responseProperties = [];
					if ($metadata->getSerializeGroups()) {
						$groups = array_merge([strtolower($method)], $metadata->getSerializeGroups());
						$groups = array_unique($groups);

						foreach ($model->getProperties() as $property) {
							if (count(array_intersect($property->getGroups(), $groups))) {
								$responseProperties[$property->getSerializeName()] = $property->getType()->toJson($groups);
							}
						}
					}
					if ($metadata->getResponseBodyProperties()) {
						$responseProperties = array_merge($requestProperties, $metadata->getResponseBodyProperties());
					}
					
					$responses[$annoSerialize->code] = [
//						'description' => 'successful operation',
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
				
				$path = [

					'tags' => [ $tag->getClass() ],
//						'summary'=> 'Creates a Book resource.',
					'parameters' => $parameters,
					'responses' => $responses,
				];
				if ($requestBody) {
					$path['requestBody'] = $requestBody;
				}
				
				$paths[$url][strtolower($method)] = $path;
			}
			
		}
		
		$request = $this->requestStack->getMasterRequest();
		
		return [
			'spec' => [
				'openapi' => '3.0.2',
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
}