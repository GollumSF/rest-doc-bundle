<?php

namespace GollumSF\RestDocBundle\Generator;

use Doctrine\Common\Annotations\Reader;
use GollumSF\RestBundle\Annotation\Serialize;
use GollumSF\RestBundle\Annotation\Unserialize;
use GollumSF\RestDocBundle\Annotation\ApiDescribe;
use GollumSF\RestDocBundle\Generator\MetadataBuilder\MetadataBuilderInterface;
use GollumSF\RestDocBundle\Generator\ModelBuilder\Model;
use GollumSF\RestDocBundle\Generator\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\Generator\TagBuilder\Tag;
use GollumSF\RestDocBundle\Generator\TagBuilder\TagBuilderInterface;
use GollumSF\RestDocBundle\Reflection\ControllerActionExtractorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class OpenApiGenerator implements OpenApiGeneratorInterface {

	/** @var MetadataBuilderInterface */
	private $metadataBuilder;

	/** @var ModelbuilderInterface */
	private $modelbuilder;
	
	/** @var TagbuilderInterface */
	private $tagbuilder;
	
	public function __construct(
		MetadataBuilderInterface      $metadataBuilderInterface,
		ModelBuilderInterface         $modelbuilder,
		TagBuilderInterface           $tagbuilder
	) {
		$this->metadataBuilder = $metadataBuilderInterface;
		$this->modelbuilder    = $modelbuilder;
		$this->tagbuilder      = $tagbuilder;
	}

	
	public function generate(): array
	{

		$paths = [];
		
		foreach ($this->metadataBuilder->getMetadataCollection() as $metadata) {


			$route           = $metadata->getRoute();
			$entity          = $metadata->getEntity();
			$isCollection    = $metadata->isCollection();
			$annoSerialize   = $metadata->getSerialize();
			$annoUnserialize = $metadata->getUnserialize();
			
			$tag = $this->tagbuilder->gettag($entity);
			$model = $this->modelbuilder->getModel($entity);
			
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
						'ref' => '#/definitions/'.$model->getClass()
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
					
					$properties = [];
					foreach ($model->getProperties() as $property) {
						if (count(array_intersect($property->getGroups(), $groups))) {
							$property = [
								'type' => $property->getType()
							];
							$properties[$property->getSerializeName()] = $property;
							
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
						'tags' => [ $tag->getClass() ],
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
				'version' => '1.0.0',
				'title' => 'Swagger Title'
			],
			'externalDocs' => [
				'description' => 'Descript doc externe',
				'url' => 'https://teambudd.io'
			],
			
			'host' => "dev.teambudd.io",
			'basePath' => "/api",
			'schemes' => [ 'https' ],

			'tags' => array_values(array_map(function (Tag $tag) { return $tag->toJson(); }, $this->tagbuilder->getAllTags())),
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
			'definitions' => array_map(function (Model $model) { return $model->toJson(); }, $this->modelbuilder->getAllModels()),
		];
	}
}