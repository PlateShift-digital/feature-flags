<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\API\Repository;

use Ibexa\Core\Base\Exceptions\NotFoundException;
use PlateShift\FeatureFlagBundle\Core\Repository\Values\FeatureFlag;
use PlateShift\FeatureFlagBundle\Spi\Persistence\CreateStruct;
use PlateShift\FeatureFlagBundle\Spi\Persistence\UpdateStruct;

interface FeatureFlagService
{
    /**
     * Loads the feature for the current siteaccess.
     *
     * @param string $identifier
     * @param string $scope
     *
     * @return FeatureFlag
     *
     * @throws NotFoundException
     */
    public function load(string $identifier, string $scope): FeatureFlag;

    /**
     * Loads a list of features for the current siteaccess.
     *
     * @param string $scope
     *
     * @return array
     */
    public function list(string $scope): array;

    /**
     * Generates a new feature flag create struct.
     *
     * @return CreateStruct
     */
    public function newFeatureFlagCreateStruct(): CreateStruct;

    /**
     * Creates the feature for the current siteaccess.
     *
     * @param CreateStruct $createStruct
     *
     * @return FeatureFlag
     */
    public function create(CreateStruct $createStruct): FeatureFlag;

    /**
     * Generates a new feature flag update struct from $feature.
     *
     * @param FeatureFlag $feature
     *
     * @return UpdateStruct
     */
    public function newFeatureFlagUpdateStruct(FeatureFlag $feature): UpdateStruct;

    /**
     * Updates the feature for the current siteaccess.
     *
     * @param UpdateStruct $updateStruct
     *
     * @return FeatureFlag
     */
    public function update(UpdateStruct $updateStruct): FeatureFlag;

    /**
     * Deletes the feature for the current siteaccess.
     *
     * @param FeatureFlag $feature
     *
     * @return void
     */
    public function delete(FeatureFlag $feature): void;
}
