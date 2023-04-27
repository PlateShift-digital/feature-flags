<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\Core\Persistence;

abstract class Gateway
{
    public const TABLE_FEATURE_FLAG = 'plate_shift_feature_flag';
    public const COLUMN_IDENTIFIER = 'identifier';
    public const COLUMN_SCOPE      = 'scope';
    public const COLUMN_ENABLED    = 'enabled';

    abstract public function load(string $identifier, string $scope): ?array;

    abstract public function insert(string $identifier, string $scope, bool $enabled): void;

    abstract public function delete(string $identifier, string $scope): void;

    abstract public function update(string $identifier, string $scope, bool $enabled): void;

    abstract public function list(string $scope): array;
}
