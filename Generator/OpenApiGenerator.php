<?php

namespace GollumSF\RestDocBundle\Generator;

use Doctrine\Common\Annotations\Reader;
use GollumSF\RestBundle\Annotation\Serialize;
use GollumSF\RestBundle\Annotation\Unserialize;
use GollumSF\RestDocBundle\Annotation\Describe;
use GollumSF\RestDocBundle\Metadata\MetadataFactoryInterface;
use GollumSF\RestDocBundle\Reflection\ControllerActionExtractorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class OpenApiGenerator implements OpenApiGeneratorInterface {

	/** @var MetadataFactoryInterface */
	private $metadataFactory;
	
	/** @var ClassMetadataFactoryInterface */
	private $classMetadataFactory;
	
	/** @var NameConverterInterface  */
	private $nameConverter;
	
	public function __construct(
		MetadataFactoryInterface $metadataFactoryInterface,
		NameConverterInterface $nameConverter,
		ClassMetadataFactoryInterface $classMetadataFactory
	) {
		$this->metadataFactory = $metadataFactoryInterface;
		$this->nameConverter = $nameConverter;
		$this->classMetadataFactory = $classMetadataFactory;
	}

	
	public function generate(): array
	{

		$paths = [];
		
		foreach ($this->metadataFactory->getMetadataCollection() as $metadata) {


			$route           = $metadata->getRoute();
			$entity          = $metadata->getEntity();
			$isCollection    = $metadata->isCollection();
			$annoSerialize   = $metadata->getSerialize();
			$annoUnserialize = $metadata->getUnserialize();

			$url = $route->getPath();
			$methods = $route->getMethods();
			$url = substr($url, strlen('/api'));

			$parameters = [];
			$responses = [];
			
			if (!isset($paths[$url])) {
				$paths[$url] = [];
			}

			/** @var Serialize $annoSerialize */
			/** @var Unserialize $annoUnserialize */
			
			if ($annoSerialize) {
				$responses[$annoSerialize->code] = [
//						'description' => 'successful operation',
					'schema' => [
						'ref' => '#/definitions/User'
					]
				];
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
			
			foreach ($methods as $method) {


				if ($annoUnserialize) {
					$groups = [ strtolower($method) ];
					if ($annoUnserialize->groups) {
						$annoGroups = $annoUnserialize->groups;
						if (!\is_array($annoGroups)) {
							$annoGroups= [ $annoGroups ];
						}
						$groups = array_merge($groups, $annoGroups);	
					}
					
					$metadata = $this->classMetadataFactory->getMetadataFor($entity);

					$properties = [];
					foreach ($metadata->getAttributesMetadata() as $attributesMetadatum) {
						if (count(array_intersect($attributesMetadatum->getGroups(), $groups))) {
							$serializeName = $attributesMetadatum->getSerializedName();
							if (!$serializeName) {
								$serializeName = $this->nameConverter->normalize(
									$attributesMetadatum->getName()
								);
							}
							$property = [
								'type' => 'string'
							];
							$properties[$serializeName] = $property;
							
						}
					}
					$parameters[] = [
						'name' => 'body',
						'in' => 'body',
						'required' => true,
						'schema' => [
							'type' => 'object',
							'properties' => $properties
						]
					];
				}
				
				$paths[$url][strtolower($method)] = [
//						'tags' => [ 'User' ],
						'parameters' => $parameters,
						'responses' => $responses,
//							'responses' => [
//							200 => [
//								'description' => 'successful operation',
//								'schema' => [
//									'ref' => '#/definitions/User'
//								]
//							]
//						]
				];
			}
			
		}
		
		return [
			'swagger' => "2.0",
			'info' => [
				'description' => 'description API',
				'version' => '1.0.3',
				'title' => 'Swagger Petstore'
			],
			'host' => "dev.teambudd.io",
			'basePath' => "/api",
			'schemes' => [ 'https' ],

			'tags' => [
				[
					'name' => 'User',
					'description' => 'Description User',
					'externalDocs' => [
						'description' => "Find out more about our store",
						'url' => "https://teambudd.io"
					]
				],
				[
					'name' => 'Game',
				],
			],
			'paths' => $paths,
//			'paths' => [
//				'/users/me' => [
//					'get' => [
//						'tags' => [ 'User' ],
////						'summary' => 'Find or create current user',
////						'description' => 'Find or create current user',
////						'produces' => [ 'application/json' ],
//						'parameters' => [],
//						'responses' => [
//							200 => [
//								'description' => 'successful operation',
//								'schema' => [
//									'ref' => '#/definitions/User'
//								]
//							]
//						],
////						'security' => []
//					]
//				],
//			],
			'securityDefinitions' => [

			],
			'definitions' => [
				'User' => [
					'type' => 'object',
					'properties' => [
						'id' => [
							'type' => 'integer',
							'format' => 'int64'
						],
						'email' => [
							'type' => 'string'
						]
					],
					'xml' => [
						'name' => 'User'
					]
				]
			],

			'externalDocs' => [
				'description' => 'Descript doc externe',
				'url' => 'https://teambudd.io'
			]
		];
	}
}