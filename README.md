# GollumSF/RestDocBundle

Auto generate documentation for GollumSF/RestBundle

## Configuration: 

All configurations is optionals. Edit file `config/packages/gollum_sf_rest_doc.yaml` :
```yaml
gollum_sf_rest_doc:
    title: 'REST Api'                      # optional, default : REST Api
    version: '1.0.0'                       # optional, default : 1.0.0
    description: 'Api general description' # optional, default : null
    
    host:                                  # optional, default : null (return current host url)
        - 'dev.api.com'
        - 'preprod.api.com'
        - 'prod.api.com'
    default_host: 'dev.api.com'            # optional, default : null (return first item to host list)
    protocol:                              # optional, default : null (return current sheme url)
        - 'http'                           
        - 'https'
    default_protocol: 'http'               # optional, default : null (return first item to protocol list)

    external_docs:                                          # optional
        url: 'https://github.com/GollumSF/rest-doc-bundle'  # required
        description: 'External documentation description'   # optional, default : null
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