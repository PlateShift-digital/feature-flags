<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\Spi\Persistence;

class UpdateStruct
{
    protected FeatureFlag $feature;

    public bool $enabled;

    public function __construct(FeatureFlag $feature)
    {
        $this->feature = $feature;
    }

    public function getFeature(): FeatureFlag
    {
        return $this->feature;
    }
}
