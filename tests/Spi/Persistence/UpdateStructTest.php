<?php

namespace Test\PlateShift\FeatureFlagBundle\Spi\Persistence;

use PlateShift\FeatureFlagBundle\Spi\Persistence\FeatureFlag;
use PlateShift\FeatureFlagBundle\Spi\Persistence\UpdateStruct;
use PHPUnit\Framework\TestCase;

class UpdateStructTest extends TestCase
{

    public function test_get_feature(): void
    {
        $feature = new FeatureFlag();

        $struct = new UpdateStruct($feature);

        $this->assertEquals($feature, $struct->getFeature());
    }
}
