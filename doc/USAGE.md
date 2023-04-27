# Usage

## Checking features in code

### php

The `@plateshift.repository.service.feature_flag`-service is used for internal handling but is publicly available.

Using the `@plateshift.feature_flag.repository`-service is the recommended way. This "Wrapper" has some extra logic like
and is taking care of response-tagging and scope-resolution falling back to the next scope in succession until a defined
state is found or the configured default-value is used.

```php
/** @var PlateShift\FeatureFlagBundle\API\FeatureFlagRepository $featureFlagRepository */
$featureFlagRepository->isEnabled('foo_feature');
$featureFlagRepository->isDisabled('bar_feature');
```

### twig

Functions to check a feature in `twig` are available.

```twig
{% if is_feature_enabled('foo_feature') %}
    feature is enabled
{% endif %}

{% if is_feature_disabled('bar_feature') %}
    feature is disabled
{% endif %}
```

## Exposing features for client processing

Features can be exposed to a javascript-friendly context using either json, javascript or data-attributes.

Note: All of those functions export the result unescaped.

#### json

You can expose the features in a simple json format.

```twig
{{ expose_features_json() }}
```

This will result in an json-string containing an object with identifiers as keys and one boolean
attribute "`enabled`".

```json
{"feature_foo": {"enabled": true}, "feature_bar": {"enabled": false}}
```

#### data-attributes

Will output data-attributes to use on dom-elements.

```twig
<body {{ expose_features_data_attributes() }}>
```

This will result in the following html-snippet:

```html
<body data-feature-foo="true" data-feature-bar="false">
```

Note that the `_` will be replaced by `-`. Javascript-side access will be `body.dataset.featureFoo`.

#### javascript

Exposing to javascript will use the json-function to assign the value to a window-attribute.

```twig
Exposure in "window.ipFeatures":
{{ expose_features_javascript() }}

Or with adjusted key in the window object (will write the states to "window.features"):
{{ expose_features_javascript('features') }}
```

The javascript can be adjusted by overriding `@ibexadesign/feature_flag/expose/javascript.html.twig`.
