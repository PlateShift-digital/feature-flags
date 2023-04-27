<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\Spi\Persistence;

class CreateStruct
{
    public string $identifier;

    public string $scope;

    public bool $enabled;
}
