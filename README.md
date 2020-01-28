# GollumSF/RestDocBundle

Auto generate documentation for GollumSF/RestBundle

## Configuration: 

File `config/packages/gollum_sf_rest_doc.yaml` :
```yaml
gollum_sf_rest_doc:
    title: 'REST Api'                      # optional, default : REST Api
    version: '1.0.0'                       # optional, default : 1.0.0
    description: 'Api general description' # optional, default : null
    

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