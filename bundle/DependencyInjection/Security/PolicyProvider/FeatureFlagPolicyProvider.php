<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\DependencyInjection\Security\PolicyProvider;

use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\YamlPolicyProvider;

class FeatureFlagPolicyProvider extends YamlPolicyProvider
{
    public function getFiles(): array
    {
        return [
            __DIR__ . '/../../../Resources/config/policies.yaml',
        ];
    }
}
