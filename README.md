# GollumSF/RestDocBundle

[![Build Status](https://travis-ci.org/GollumSF/rest-doc-bundle.svg?branch=master)](https://travis-ci.org/GollumSF/rest-doc-bundle)
[![Coverage](https://coveralls.io/repos/github/GollumSF/rest-doc-bundle/badge.svg?branch=master)](https://coveralls.io/github/GollumSF/rest-doc-bundle)
[![License](https://poser.pugx.org/gollumsf/rest-doc-bundle/license)](https://packagist.org/packages/gollumsf/rest-doc-bundle)
[![Latest Stable Version](https://poser.pugx.org/gollumsf/rest-doc-bundle/v/stable)](https://packagist.org/packages/gollumsf/rest-doc-bundle)
[![Latest Unstable Version](https://poser.pugx.org/gollumsf/rest-doc-bundle/v/unstable)](https://packagist.org/packages/gollumsf/rest-doc-bundle)
[![Discord](https://img.shields.io/discord/671741944149573687?color=purple&label=discord)](https://discord.gg/xMBc5SQ)

Auto-Generate documentation API for GollumSF\RestBundle : https://github.com/GollumSF/rest-bundle

## Installation:

```shell
composer require gollumsf/rest-doc-bundle
```

### config/bundles.php
```php
return [
    // [ ... ]
    GollumSF\RestBundle\GollumSFRestDocBundle::class => ['all' => true],
];
```

## Configuration: 

All configurations is optionals. Edit file `config/packages/gollum_sf_rest_doc.yaml` :
```yaml
gollum_sf_rest_doc:

    #################
    # Documentation #
    #################

    title: 'REST Api'                      # optional, default : REST Api
    version: '1.0.0'                       # optional, default : 1.0.0
    description: 'Api general description' # optional, default : null

    external_docs:                                          # optional
        url: 'https://github.com/GollumSF/rest-doc-bundle'  # required
        description: 'External documentation description'   # optional, default : null
    
    ##############
    # Host / URL #
    ################
    
    host:                                  # optional, default : null (return current host url)
        - 'dev.api.com'
        - 'preprod.api.com'
        - 'prod.api.com'
    default_host: 'dev.api.com'            # optional, default : null (return first item to host list)
    protocol:                              # optional, default : null (return current sheme url)
        - 'http'                           
        - 'https'
    default_protocol: 'http'               # optional, default : null (return first item to protocol list)
    
    ############
    # Security #
    ############

    security:                              # optional (No security token if not defined)
        my_first_configuration:
            type: 'authorization_bearer'   # required, authorization_bearer generate classic authorization bearer
            name: 'Authorization'          # optional, default: Authorization, the header name 
            scheme: 'BEARER'               # optional, default: BEARER, the scheme in header value
            defaultValue: 'TOKEN_DEMO'     # optional, the default token value for demo
            
        my_second_configuration:
            type: 'query_param'            # required, query_param generate query string token
            name: 'token'                  # optional, default: token, the query name
            defaultValue: 'TOKEN_DEMO'     # optional, the default token value for demo
        my_custom_configuration:
            type: 'custom'                 # required, custom generate a custom configuration based on:
            defaultValue: 'TOKEN_DEMO'     # optional, the default token value for demo
            data:                          # required, Data based on securitySchemes content 
                type: 'http'               #  - show : https://swagger.io/docs/specification/authentication/
                scheme: 'basic'

```

## Integration with [Swagger](https://github.com/swagger-api/swagger-ui): 

```yaml
#app/config/routing.yml
gsf_restbundle_swagger:
    resource: "@GollumSFRestDocBundle/Resources/config/swagger_routing.yml"
    prefix: /api-docs
```

## Integration with OpenApi JSON: 

```yaml
gsf_restbundle_openapi:
    resource: "@GollumSFRestDocBundle/Resources/config/openapi_routing.yml"
    prefix: /api-docs.json
```

## Annotations: 