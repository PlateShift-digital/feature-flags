<?php

namespace PlateShift\FeatureFlagBundle\API\Repository\Values\User\Limitation;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;

class FeatureGroupsLimitation extends Limitation
{
    public const FEATURE_GROUP = 'FeatureGroups';

    public function getIdentifier(): string
    {
        return self::FEATURE_GROUP;
    }
}
