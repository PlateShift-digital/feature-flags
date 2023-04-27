# PlateShift Feature Flag Bundle

PlateShift Feature Flag Bundle is an eZ Platform bundle to handle feature-control giving you more control over when a
feature goes live.

Features can be checked for `enabled` and `disabled` state to save on precious exclamation marks (and headaches
overlooking them)!

## Installation

Add the bundle to your `config/bundles.php`

```php
<?php

return [
    // ...
    PlateShift\FeatureFlagBundle\PlateShift\FeatureFlagBundle::class => ['all' => true],
    // ...
];
```

Add the routing configuration at `config/routes/plate_shift_feature_flag.yaml` (or anywhere else it gets included)

```yaml
_plateshiftFeatureFlags:
    resource: '@PlateShiftFeatureFlagBundle/Resources/config/routing.yaml'
```

## Configuration

Refer to the [configuration documentation](doc/CONFIGURATION.md).

## Usage

Refer to the [usage documentation](doc/USAGE.md).

## Changelog

Refer to the [changelog documentation](doc/CHANGELOG.md) or the 
[github release overview](https://github.com/intenseprogramming/feature-flag-bundle/releases).
