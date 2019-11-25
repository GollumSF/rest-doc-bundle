# GollumSF/RestDocBundle

Auto generate documentation for GollumSF/RestBundle


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