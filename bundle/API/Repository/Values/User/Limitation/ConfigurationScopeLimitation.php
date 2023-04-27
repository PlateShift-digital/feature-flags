<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\API\Repository\Values\User\Limitation;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;

class ConfigurationScopeLimitation extends Limitation
{
    public const CONFIGURATION_SCOPE = 'ConfigurationScope';

    public function getIdentifier(): string
    {
        return self::CONFIGURATION_SCOPE;
    }
}
