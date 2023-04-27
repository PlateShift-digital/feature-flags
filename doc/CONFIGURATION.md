
## Configuration

To add a feature you can add new `yaml`-file in `config/packages/` with the following content

```yaml
plate_shift_feature_flag:
    features:
        foo_feature:
            name: Example feature
            description: An example feature.
            default: true
            exposed: true
        bar_feature:
            name:
                id: feature.with.translation.name
                context: feature_translation_context
            description:
                id: feature.with.translation.description
                context: feature_translation_context
            default: false
```

The name and description of features can be translated using `id` and `context`. The translation will be handled by the
`@translator`-service of symfony.
