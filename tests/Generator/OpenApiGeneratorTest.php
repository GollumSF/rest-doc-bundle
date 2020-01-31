<?php

namespace Test\GollumSF\RestDocBundle\Generator;

use GollumSF\ReflectionPropertyTest\ReflectionPropertyTrait;
use GollumSF\RestBundle\Annotation\Serialize;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\Metadata;
use GollumSF\RestDocBundle\Builder\MetadataBuilder\MetadataBuilderInterface;
use GollumSF\RestDocBundle\Builder\ModelBuilder\ModelBuilderInterface;
use GollumSF\RestDocBundle\Builder\TagBuilder\Tag;
use GollumSF\RestDocBundle\Builder\TagBuilder\TagBuilderInterface;
use GollumSF\RestDocBundle\Configuration\ApiDocConfigurationInterface;
use GollumSF\RestDocBundle\Generator\OpenApiGenerator;
use GollumSF\RestDocBundle\Generator\Parameters\ParameterCollection;
use GollumSF\RestDocBundle\Generator\Parameters\ParametersGeneratorInterface;
use GollumSF\RestDocBundle\Generator\RequestBody\RequestBodyGeneratorInterface;
use GollumSF\RestDocBundle\Generator\RequestBody\RequestBodyPropertyCollection;
use GollumSF\RestDocBundle\Generator\ResponseProperties\ResponsePropertiesGeneratorInterface;
use GollumSF\RestDocBundle\Generator\ResponseProperties\ResponsePropertyCollection;
use GollumSF\RestDocBundle\Generator\Security\SecurityCollection;
use GollumSF\RestDocBundle\Generator\Security\SecurityGeneratorInterface;
use GollumSF\RestDocBundle\TypeDiscover\Models\ObjectType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

class OpenApiGeneratorTestGenerate extends OpenApiGenerator {
	
	public $externalDocs = null;
	
	protected function generateInfo(): array {
		return [ 'INFO' ];
	}
	protected function generateServers(): array {
		return [ 'SERVERS' ];
	}
	protected function generatePaths(): array {
		return [ 'PATHS' ];
	}
	protected function generateExternalDocs(): ?array {
		return $this->externalDocs;
	}
	protected function generateSecurity(): array {
		return [ 'SECURITY' => [ 'SECURITY_DATA' ] ];
	}
}

class OpenApiGeneratorTestGetBasePath extends OpenApiGenerator {

	public $basePath = '';
	
	protected function getBasePath(): string {
		return $this->basePath;
	}
	
	protected function generateParameters($url, Metadata $metadata, string $method): array {
		return [ 'PARAMETER' => $url, 'METHOD' => $method ];
	}

	protected function generateResponse(Metadata $metadata, string $method): array {
		return [ 'RESPONSE' => 'VALUE', 'METHOD' => $method ];
	}

	protected function generateRequestBody(Metadata $metadata, string $method): array {
		return [ 'REQUEST_BODY' => 'VALUE', 'METHOD' => $method ];
	}

	protected function hasRequestBody(Metadata $metadata, string $method): bool {
		return $metadata->hasRequestBody;
	}
}

class OpenApiGeneratorTestGenerateServers extends OpenApiGenerator {
	protected function getBasePath(): string {
		return '/base/path';
	}
}

class OpenApiGeneratorTest extends TestCase {
	
	use ReflectionPropertyTrait;
	
	private $metadataBuilder;
	private $modelbuilder;
	private $tagbuilder;
	private $requestStack;
	private $parametersGenerator;
	private $responsePropertiesGenerator;
	private $requestBodyGenerator;
	private $securityGenerator;
	private $apiDocConfiguration;

	public function setUp(): void {
		$this->metadataBuilder             = $this->getMockForAbstractClass(MetadataBuilderInterface::class);
		$this->modelbuilder                = $this->getMockForAbstractClass(ModelBuilderInterface::class);
		$this->tagbuilder                  = $this->getMockForAbstractClass(TagBuilderInterface::class);
		$this->requestStack                = $this->getMockBuilder(RequestStack::class)->disableOriginalConstructor()->getMock();
		$this->parametersGenerator         = $this->getMockForAbstractClass(ParametersGeneratorInterface::class);
		$this->responsePropertiesGenerator = $this->getMockForAbstractClass(ResponsePropertiesGeneratorInterface::class);
		$this->requestBodyGenerator        = $this->getMockForAbstractClass(RequestBodyGeneratorInterface::class);
		$this->securityGenerator           = $this->getMockForAbstractClass(SecurityGeneratorInterface::class);
		$this->apiDocConfiguration         = $this->getMockForAbstractClass(ApiDocConfigurationInterface::class);
	}
	
	public function providerGenerate() {
		return [
			[
				[ 'EXTERNAL_DOCS' ], [
					'openapi' => OpenApiGenerator::OPEN_API_VERSION,
					'info'    => [ 'INFO' ],
					'servers' => [ 'SERVERS' ],
					'paths'   => [ 'PATHS' ],
					'tags'    => [
						[ 'name' => 'ClassEntity1' ],
						[ 'name' => 'ClassEntity2' ],
						[ 'name' => 'ClassEntity3' ],
					],
					'schemas' => [
						'ClassEntity1' => [ 'type' => 'object', 'properties' => [], 'xml' => [ 'name' => 'ClassEntity1' ] ],
						'ClassEntity2' => [ 'type' => 'object', 'properties' => [], 'xml' => [ 'name' => 'ClassEntity2' ] ],
						'ClassEntity3' => [ 'type' => 'object', 'properties' => [], 'xml' => [ 'name' => 'ClassEntity3' ] ],
					],
					'components' => [
						'securitySchemes' => [ 'SECURITY' => [ 'SECURITY_DATA' ] ]
					],
					'security' => [ [ 'SECURITY' => [] ] ],
					'externalDocs' => [ 'EXTERNAL_DOCS' ],
				] 
			],
			[
				null, [
				'openapi' => OpenApiGenerator::OPEN_API_VERSION,
				'info'    => [ 'INFO' ],
				'servers' => [ 'SERVERS' ],
				'paths'   => [ 'PATHS' ],
				'tags'    => [
					[ 'name' => 'ClassEntity1' ],
					[ 'name' => 'ClassEntity2' ],
					[ 'name' => 'ClassEntity3' ],
				],
				'schemas' => [
					'ClassEntity1' => [ 'type' => 'object', 'properties' => [], 'xml' => [ 'name' => 'ClassEntity1' ] ],
					'ClassEntity2' => [ 'type' => 'object', 'properties' => [], 'xml' => [ 'name' => 'ClassEntity2' ] ],
					'ClassEntity3' => [ 'type' => 'object', 'properties' => [], 'xml' => [ 'name' => 'ClassEntity3' ] ],
				],
				'components' => [
					'securitySchemes' => [ 'SECURITY' => [ 'SECURITY_DATA' ] ]
				],
				'security' => [ [ 'SECURITY' => [] ] ],
			]
			]
		];
	}
	
	/**
	 * @dataProvider providerGenerate
	 */
	public function testGenerate($externalDocs, $result) {
		
		$openApiGenerator = new OpenApiGeneratorTestGenerate(
			$this->metadataBuilder,
			$this->modelbuilder,
			$this->tagbuilder,
			$this->requestStack,
			$this->parametersGenerator,
			$this->responsePropertiesGenerator,
			$this->requestBodyGenerator,
			$this->securityGenerator,
			$this->apiDocConfiguration
		);
		
		$tag1 = $this->getMockBuilder(Tag::class)->disableOriginalConstructor()->getMock();
		$tag2 = $this->getMockBuilder(Tag::class)->disableOriginalConstructor()->getMock();
		$tag3 = $this->getMockBuilder(Tag::class)->disableOriginalConstructor()->getMock();

		$tag1->expects($this->once())->method('toJson')->willReturn([ 'name' => 'ClassEntity1' ]);
		$tag2->expects($this->once())->method('toJson')->willReturn([ 'name' => 'ClassEntity2' ]);
		$tag3->expects($this->once())->method('toJson')->willReturn([ 'name' => 'ClassEntity3' ]);
		
		$this->tagbuilder
			->expects($this->once())
			->method('getAllTags')
			->willReturn([
				'ClassEntity1' => $tag1,
				'ClassEntity2' => $tag2,
				'ClassEntity3' => $tag3,
			])
		;

		$model1 = $this->getMockBuilder(ObjectType::class)->disableOriginalConstructor()->getMock();
		$model2 = $this->getMockBuilder(ObjectType::class)->disableOriginalConstructor()->getMock();
		$model3 = $this->getMockBuilder(ObjectType::class)->disableOriginalConstructor()->getMock();

		$model1->expects($this->once())->method('toJsonRef')->willReturn([ 'type' => 'object', 'properties' => [], 'xml' => [ 'name' => 'ClassEntity1' ] ]);
		$model2->expects($this->once())->method('toJsonRef')->willReturn([ 'type' => 'object', 'properties' => [], 'xml' => [ 'name' => 'ClassEntity2' ] ]);
		$model3->expects($this->once())->method('toJsonRef')->willReturn([ 'type' => 'object', 'properties' => [], 'xml' => [ 'name' => 'ClassEntity3' ] ]);

		$this->modelbuilder
			->expects($this->once())
			->method('getAllModels')
			->willReturn([
				'ClassEntity1' => $model1,
				'ClassEntity2' => $model2,
				'ClassEntity3' => $model3,
			])
		;
		
		$openApiGenerator->externalDocs = $externalDocs;
		
		$json = $openApiGenerator->generate();
		
		$this->assertEquals($json, $result);
	}

	public function providerBasePath() {
		return [
			[ [], '' ],
			[ [ '/api' ], '/api' ],
			[ [ '/api/' ], '/api' ],
			[ [ '/' ], '' ],
			[ [
				'/api/users',
				'/api/games',
				'/api/stores/',
			], '/api' ],
		];
	}

	/**
	 * @dataProvider providerBasePath
	 */
	public function testGetBasePath($urls, $result) {

		$openApiGenerator = new OpenApiGenerator(
			$this->metadataBuilder,
			$this->modelbuilder,
			$this->tagbuilder,
			$this->requestStack,
			$this->parametersGenerator,
			$this->responsePropertiesGenerator,
			$this->requestBodyGenerator,
			$this->securityGenerator,
			$this->apiDocConfiguration
		);

		$metadatas = [];
		foreach ($urls as $url) {
			$route = $this->getMockBuilder(Route::class)->disableOriginalConstructor()->getMock();
			$route
				->expects($this->once())
				->method('getPath')
				->willReturn($url)
			;
			$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
			$metadata
				->expects($this->once())
				->method('getRoute')
				->willReturn($route)
			;
			$metadatas[] = $metadata;
		}
		
		$this->metadataBuilder
			->expects($this->once())
			->method('getMetadataCollection')
			->willReturn($metadatas)
		;

		$this->assertEquals(
			$this->reflectionCallMethod($openApiGenerator, 'getBasePath'), $result
		);
	}

	public function providerGeneratePaths() {
		return [
			[ '', [], [] ],
			
			[ '', [
				// class, path, methods, hasResquestBody
				[ 'Class1', '/api/users', ['GET'], true ],
				[ 'Class1', '/api/users/list', ['GET', 'POST'], false ],
				[ 'Class2', '/api/games', ['PATCH'], false ],
			], [
				'/api/users' => [
					'get' => [
						'tags' => [ 'Class1' ],
						'parameters' => [ 'PARAMETER' => '/api/users', 'METHOD' => 'GET' ],
						'responses' => [ 'RESPONSE' => 'VALUE', 'METHOD' => 'GET' ],
						'requestBody' => [ 'REQUEST_BODY' => 'VALUE', 'METHOD' => 'GET' ],
					]
				],
				'/api/users/list' => [
					'get' => [
						'tags' => [ 'Class1' ],
						'parameters' => [ 'PARAMETER' => '/api/users/list', 'METHOD' => 'GET' ],
						'responses' => [ 'RESPONSE' => 'VALUE', 'METHOD' => 'GET' ],
					],
					'post' => [
						'tags' => [ 'Class1' ],
						'parameters' => [ 'PARAMETER' => '/api/users/list', 'METHOD' => 'POST' ],
						'responses' => [ 'RESPONSE' => 'VALUE', 'METHOD' => 'POST' ],
					]
				],
				'/api/games' => [
					'patch' => [
						'tags' => [ 'Class2' ],
						'parameters' => [ 'PARAMETER' => '/api/games', 'METHOD' => 'PATCH' ],
						'responses' => [ 'RESPONSE' => 'VALUE', 'METHOD' => 'PATCH' ],
					]
				]
			] ],

			[ '/api', [
				// class, path, methods, hasResquestBody
				[ 'Class1', '/api/users', ['GET'], true ],
				[ 'Class1', '/api/users/list', ['GET', 'POST'], false ],
				[ 'Class2', '/api/games', ['PATCH'], false ],
			], [
				'/users' => [
					'get' => [
						'tags' => [ 'Class1' ],
						'parameters' => [ 'PARAMETER' => '/users', 'METHOD' => 'GET' ],
						'responses' => [ 'RESPONSE' => 'VALUE', 'METHOD' => 'GET' ],
						'requestBody' => [ 'REQUEST_BODY' => 'VALUE', 'METHOD' => 'GET' ],
					]
				],
				'/users/list' => [
					'get' => [
						'tags' => [ 'Class1' ],
						'parameters' => [ 'PARAMETER' => '/users/list', 'METHOD' => 'GET' ],
						'responses' => [ 'RESPONSE' => 'VALUE', 'METHOD' => 'GET' ],
					],
					'post' => [
						'tags' => [ 'Class1' ],
						'parameters' => [ 'PARAMETER' => '/users/list', 'METHOD' => 'POST' ],
						'responses' => [ 'RESPONSE' => 'VALUE', 'METHOD' => 'POST' ],
					]
				],
				'/games' => [
					'patch' => [
						'tags' => [ 'Class2' ],
						'parameters' => [ 'PARAMETER' => '/games', 'METHOD' => 'PATCH' ],
						'responses' => [ 'RESPONSE' => 'VALUE', 'METHOD' => 'PATCH' ],
					]
				]
			] ],
		];
	}

	/**
	 * @dataProvider providerGeneratePaths
	 */
	public function testGeneratePaths($basePath, $metadataInfos, $result) {
		
		$openApiGenerator = new OpenApiGeneratorTestGetBasePath (
			$this->metadataBuilder,
			$this->modelbuilder,
			$this->tagbuilder,
			$this->requestStack,
			$this->parametersGenerator,
			$this->responsePropertiesGenerator,
			$this->requestBodyGenerator,
			$this->securityGenerator,
			$this->apiDocConfiguration
		);
		$openApiGenerator->basePath = $basePath;

		$metadatas = [];
		foreach ($metadataInfos as $i => $metadataInfo) {

			$route = $this->getMockBuilder(Route::class)->disableOriginalConstructor()->getMock();
			$route
				->expects($this->at(0))
				->method('getPath')
				->willReturn($metadataInfo[1])
			;
			$route
				->expects($this->at(1))
				->method('getMethods')
				->willReturn($metadataInfo[2])
			;
			
			$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
			$metadata
				->expects($this->at(0))
				->method('getRoute')
				->willReturn($route)
			;
			$metadata
				->expects($this->at(1))
				->method('getEntity')
				->willReturn($metadataInfo[0])
			;
			$metadata->hasRequestBody = $metadataInfo[3];
			$metadatas[] = $metadata;
			
			$tag = $this->getMockBuilder(Tag::class)->disableOriginalConstructor()->getMock();
			$tag
				->method('getClass')
				->willReturn($metadataInfo[0])
			;
			$this->tagbuilder
				->expects($this->at($i))
				->method('getTag')
				->with($metadataInfo[0])
				->willReturn($tag)
			;
		}

		$this->metadataBuilder
			->expects($this->once())
			->method('getMetadataCollection')
			->willReturn($metadatas)
		;
		
		$this->assertEquals(
			$this->reflectionCallMethod($openApiGenerator, 'generatePaths'), $result
		);
	}
	
	public function testGenerateParameters() {
		$openApiGenerator = new OpenApiGenerator (
			$this->metadataBuilder,
			$this->modelbuilder,
			$this->tagbuilder,
			$this->requestStack,
			$this->parametersGenerator,
			$this->responsePropertiesGenerator,
			$this->requestBodyGenerator,
			$this->securityGenerator,
			$this->apiDocConfiguration
		);

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		
		$collection = new ParameterCollection();
		$collection->add([ 'name' => 'PARAM1' ]);
		$collection->add([ 'name' => 'PARAM2' ]);
		
		$this->parametersGenerator
			->expects($this->once())
			->method('generate')
			->with('URL', $metadata, 'GET')
			->willReturn($collection)
		;

		$this->assertEquals(
			$this->reflectionCallMethod($openApiGenerator, 'generateParameters', ['URL', $metadata, 'GET']), [
				[ 'name' => 'PARAM1' ],
				[ 'name' => 'PARAM2' ],
			]
		);
	}

	public function providerGenerateResponse() {
		return [
			[ null, [] ],
			[ new Serialize([]), [
				Response::HTTP_OK => [
					'content' => [
						'application/json' => [
							'schema' => [
								'type' => 'object',
								'properties' => [
									'PROP1' => [ 'name' => 'PARAM1' ],
									'PROP2' => [ 'name' => 'PARAM2' ],
								]
							]
						]
					]
				]
			] ],
			[ new Serialize(['code' => Response::HTTP_INTERNAL_SERVER_ERROR]), [
				Response::HTTP_INTERNAL_SERVER_ERROR => [
					'content' => [
						'application/json' => [
							'schema' => [
								'type' => 'object',
								'properties' => [
									'PROP1' => [ 'name' => 'PARAM1' ],
									'PROP2' => [ 'name' => 'PARAM2' ],
								]
							]
						]
					]
				]
			] ],
		];
	}

	/**
	 * @dataProvider providerGenerateResponse
	 */
	public function testGenerateResponse($anno, $result) {
		$openApiGenerator = new OpenApiGenerator (
			$this->metadataBuilder,
			$this->modelbuilder,
			$this->tagbuilder,
			$this->requestStack,
			$this->parametersGenerator,
			$this->responsePropertiesGenerator,
			$this->requestBodyGenerator,
			$this->securityGenerator,
			$this->apiDocConfiguration
		);

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();
		$metadata
			->expects($this->once())
			->method('getSerialize')
			->willReturn($anno)
		;
		
		$collection = new ResponsePropertyCollection();
		$collection->add('PROP1', [ 'name' => 'PARAM1' ]);
		$collection->add('PROP2', [ 'name' => 'PARAM2' ]);

		$this->responsePropertiesGenerator
			->method('generate')
			->with($metadata, 'GET')
			->willReturn($collection)
		;

		$this->assertEquals(
			$this->reflectionCallMethod($openApiGenerator, 'generateResponse', [$metadata, 'GET']), $result
		);
	}

	public function testHasRequestBody() {
		$openApiGenerator = new OpenApiGenerator (
			$this->metadataBuilder,
			$this->modelbuilder,
			$this->tagbuilder,
			$this->requestStack,
			$this->parametersGenerator,
			$this->responsePropertiesGenerator,
			$this->requestBodyGenerator,
			$this->securityGenerator,
			$this->apiDocConfiguration
		);

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();

		$this->requestBodyGenerator
			->expects($this->at(0))
			->method('hasRequestBody')
			->with($metadata, 'GET')
			->willReturn(true)
		;
		$this->requestBodyGenerator
			->expects($this->at(1))
			->method('hasRequestBody')
			->with($metadata, 'GET')
			->willReturn(false)
		;

		$this->assertEquals(
			$this->reflectionCallMethod($openApiGenerator, 'hasRequestBody', [$metadata, 'GET']), true
		);
		$this->assertEquals(
			$this->reflectionCallMethod($openApiGenerator, 'hasRequestBody', [$metadata, 'GET']), false
		);
	}

	public function testGenerateRequestBody() {
		$openApiGenerator = new OpenApiGenerator (
			$this->metadataBuilder,
			$this->modelbuilder,
			$this->tagbuilder,
			$this->requestStack,
			$this->parametersGenerator,
			$this->responsePropertiesGenerator,
			$this->requestBodyGenerator,
			$this->securityGenerator,
			$this->apiDocConfiguration
		);

		$metadata = $this->getMockBuilder(Metadata::class)->disableOriginalConstructor()->getMock();

		$collection = new RequestBodyPropertyCollection();
		$collection->add('PROP1', [ 'name' => 'PARAM1' ]);
		$collection->add('PROP2', [ 'name' => 'PARAM2' ]);
		
		$this->requestBodyGenerator
			->expects($this->once())
			->method('generateProperties')
			->with($metadata, 'GET')
			->willReturn($collection)
		;
		
		$this->assertEquals(
			$this->reflectionCallMethod($openApiGenerator, 'generateRequestBody', [$metadata, 'GET']), [
				'content' => [
					'application/json' => [
						'schema' => [
							'type' => 'object',
							'properties' => [
								'PROP1' => [ 'name' => 'PARAM1' ],
								'PROP2' => [ 'name' => 'PARAM2' ],
							]
						]
					]
				]
			]
		);
	}

	public function testGenerateInfo() {
		$openApiGenerator = new OpenApiGenerator (
			$this->metadataBuilder,
			$this->modelbuilder,
			$this->tagbuilder,
			$this->requestStack,
			$this->parametersGenerator,
			$this->responsePropertiesGenerator,
			$this->requestBodyGenerator,
			$this->securityGenerator,
			$this->apiDocConfiguration
		);

		$this->apiDocConfiguration->expects($this->once())->method('getTitle')->willReturn('TITLE');
		$this->apiDocConfiguration->expects($this->once())->method('getVersion')->willReturn('VERSION');
		$this->apiDocConfiguration->expects($this->once())->method('getDescription')->willReturn('DESCRIPTION');

		$this->assertEquals(
			$this->reflectionCallMethod($openApiGenerator, 'generateInfo'), [
				'title' => 'TITLE',
				'version' => 'VERSION',
				'description' => 'DESCRIPTION',
			]
		);
	}

	public function testGenerateInfoNoDesc() {
		$openApiGenerator = new OpenApiGenerator (
			$this->metadataBuilder,
			$this->modelbuilder,
			$this->tagbuilder,
			$this->requestStack,
			$this->parametersGenerator,
			$this->responsePropertiesGenerator,
			$this->requestBodyGenerator,
			$this->securityGenerator,
			$this->apiDocConfiguration
		);

		$this->apiDocConfiguration->expects($this->once())->method('getTitle')->willReturn('TITLE');
		$this->apiDocConfiguration->expects($this->once())->method('getVersion')->willReturn('VERSION');
		$this->apiDocConfiguration->expects($this->once())->method('getDescription')->willReturn(null);

		$this->assertEquals(
			$this->reflectionCallMethod($openApiGenerator, 'generateInfo'), [
				'title' => 'TITLE',
				'version' => 'VERSION',
			]
		);
	}

	public function providerGenerateExternalDocs() {
		return [
			[ [ 'url' => 'URL' ], [ 'url' => 'URL' ] ],
			[ [ 'url' => 'URL', 'description' => null ], [ 'url' => 'URL' ] ],
			[ [ 'url' => 'URL', 'description' => 'DESCRIPTION' ], [ 'url' => 'URL', 'description' => 'DESCRIPTION' ] ],
		];
	}

	/**
	 * @dataProvider providerGenerateExternalDocs
	 */
	public function testGenerateExternalDocs($externalDoc, $result) {

		$openApiGenerator = new OpenApiGenerator (
			$this->metadataBuilder,
			$this->modelbuilder,
			$this->tagbuilder,
			$this->requestStack,
			$this->parametersGenerator,
			$this->responsePropertiesGenerator,
			$this->requestBodyGenerator,
			$this->securityGenerator,
			$this->apiDocConfiguration
		);

		$this->apiDocConfiguration->expects($this->once())->method('getExternalDocs')->willReturn($externalDoc);
		
		$this->assertEquals(
			$this->reflectionCallMethod($openApiGenerator, 'generateExternalDocs'), $result
		);
	}

	public function testGenerateExternalDocsNoInfos() {
		$openApiGenerator = new OpenApiGenerator (
			$this->metadataBuilder,
			$this->modelbuilder,
			$this->tagbuilder,
			$this->requestStack,
			$this->parametersGenerator,
			$this->responsePropertiesGenerator,
			$this->requestBodyGenerator,
			$this->securityGenerator,
			$this->apiDocConfiguration
		);

		$this->apiDocConfiguration->expects($this->once())->method('getExternalDocs')->willReturn(null);
		$this->assertNull(
			$this->reflectionCallMethod($openApiGenerator, 'generateExternalDocs')
		);
	}
	
	public function providerGenerateServers() {
		return [
			[ [], null, [], null, [
				[
					'url' => '{protocol}://{base_uri}',
					'variables' => [
						'base_uri' => [ 
							'enum' => [
								'REQUEST_HOST/base/path'
							], 'default' => 'REQUEST_HOST'
						],
						'protocol' => [ 
							'enum' => [ 
								'REQUEST_PROTOCOL'
							], 'default' => 'REQUEST_PROTOCOL'
						]
					]
				]
			] ],

			[ [ 'HOST1', 'HOST2' ], null, [ 'PROTO1', 'PROTO2' ], null, [
				[
					'url' => '{protocol}://{base_uri}',
					'variables' => [
						'base_uri' => [
							'enum' => [
								'HOST1/base/path',
								'HOST2/base/path',
							], 'default' => 'HOST1'
						],
						'protocol' => [
							'enum' => [
								'PROTO1',
								'PROTO2',
							], 'default' => 'PROTO1'
						]
					]
				]
			] ],

			[ [ 'HOST1', 'HOST2' ], 'HOST2', [ 'PROTO1', 'PROTO2' ], 'PROTO2', [
				[
					'url' => '{protocol}://{base_uri}',
					'variables' => [
						'base_uri' => [
							'enum' => [
								'HOST1/base/path',
								'HOST2/base/path',
							], 'default' => 'HOST2'
						],
						'protocol' => [
							'enum' => [
								'PROTO1',
								'PROTO2',
							], 'default' => 'PROTO2'
						]
					]
				]
			] ],
		];
	}

	/**
	 * @dataProvider providerGenerateServers
	 */
	public function testGenerateServers($hosts, $defaultHost, $protocols, $defaultProtocol, $result) {

		$openApiGenerator = new OpenApiGeneratorTestGenerateServers (
			$this->metadataBuilder,
			$this->modelbuilder,
			$this->tagbuilder,
			$this->requestStack,
			$this->parametersGenerator,
			$this->responsePropertiesGenerator,
			$this->requestBodyGenerator,
			$this->securityGenerator,
			$this->apiDocConfiguration
		);

		$request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
		$request
			->method('getHost')
			->willReturn('REQUEST_HOST')
		;
		$request
			->method('getScheme')
			->willReturn('REQUEST_PROTOCOL')
		;
		
		$this->requestStack
			->expects($this->once())
			->method('getMasterRequest')
			->willReturn($request)
		;

		$this->apiDocConfiguration
			->method('getHost')
			->willReturn($hosts)
		;
		$this->apiDocConfiguration
			->method('getDefaultHost')
			->willReturn($defaultHost)
		;
		$this->apiDocConfiguration
			->method('getProtocol')
			->willReturn($protocols)
		;
		$this->apiDocConfiguration
			->method('getDefaultProtocol')
			->willReturn($defaultProtocol)
		;

		$host = $this->apiDocConfiguration->getHost() ? $this->apiDocConfiguration->getHost() : [ $request->getHost() ];
		$defaultEnv = $this->apiDocConfiguration->getDefaultHost() ? $this->apiDocConfiguration->getDefaultHost() : $host[0];

		$protocols = $this->apiDocConfiguration->getProtocol() ? $this->apiDocConfiguration->getProtocol() : [ $request->getScheme() ];
		$defaultProtocol = $this->apiDocConfiguration->getDefaultProtocol() ? $this->apiDocConfiguration->getDefaultProtocol() : $protocols[0];

		$this->assertEquals(
			$this->reflectionCallMethod($openApiGenerator, 'generateServers'), $result
		);
	}

	public function testGenerateSecurity() {
		$openApiGenerator = new OpenApiGenerator (
			$this->metadataBuilder,
			$this->modelbuilder,
			$this->tagbuilder,
			$this->requestStack,
			$this->parametersGenerator,
			$this->responsePropertiesGenerator,
			$this->requestBodyGenerator,
			$this->securityGenerator,
			$this->apiDocConfiguration
		);

		$collection = new SecurityCollection();
		$collection->add('NAME1', [ 'type' => 'TYPE1' ]);
		$collection->add('NAME2', [ 'type' => 'TYPE2' ]);

		$this->securityGenerator
			->expects($this->once())
			->method('generate')
			->willReturn($collection)
		;

		$this->assertEquals(
			$this->reflectionCallMethod($openApiGenerator, 'generateSecurity'), [
				'NAME1' => [ 'type' => 'TYPE1' ],
				'NAME2' => [ 'type' => 'TYPE2' ],
			]
		);
	}
}