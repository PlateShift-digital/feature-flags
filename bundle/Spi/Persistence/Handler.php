<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\Spi\Persistence;

interface Handler
{
    public function create(CreateStruct $struct): FeatureFlag;

    public function update(UpdateStruct $struct): FeatureFlag;

    public function load(string $identifier, string $scope): ?FeatureFlag;

    public function delete(FeatureFlag $feature): void;

    public function list(string $scope): array;
}
