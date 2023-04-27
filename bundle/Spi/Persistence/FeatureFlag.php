<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\Spi\Persistence;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class FeatureFlag extends ValueObject
{
    public string $identifier;

    public string $scope;

    public bool $enabled;
}
