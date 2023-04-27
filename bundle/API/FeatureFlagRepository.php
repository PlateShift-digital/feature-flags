<?php

namespace PlateShift\FeatureFlagBundle\API;

use PlateShift\FeatureFlagBundle\Core\Repository\Values\FeatureFlag;

interface FeatureFlagRepository
{
    /**
     * Return true if the feature with $identifier is enabled.
     */
    public function isEnabled(string $identifier, string $scope = null): bool;

    /**
     * Return true if the feature with $identifier is disabled.
     */
    public function isDisabled(string $identifier, string $scope = null): bool;

    /**
     * Returns a feature by $identifier.
     *
     * @internal this method does not notify the cache and should not be used for cache sensitive resources
     */
    public function get(string $identifier, string $scope = null): FeatureFlag;

    /**
     * Temporary enables a feature by $identifier. Will reset after calling FeatureFlagService::reset().
     */
    public function enableFeature(string $identifier): void;

    /**
     * Temporary disables a feature by $identifier. Will reset after calling FeatureFlagService::reset().
     */
    public function disableFeature(string $identifier): void;

    /**
     * Resets all temporary enabled/disabled features.
     */
    public function reset(): void;

    /**
     * Returns exposed features.
     */
    public function getExposedFeatureStates(): array;

    /**
     * Returns all currently possible scopes.
     */
    public function getAllScopes(): array;
}
