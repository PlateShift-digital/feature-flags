<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle;

use Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension;
use PlateShift\FeatureFlagBundle\DependencyInjection\Security\PolicyProvider\FeatureFlagPolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PlateShiftFeatureFlagBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $extension = $container->getExtension('ibexa');

        if ($extension instanceof IbexaCoreExtension) {
            $extension->addPolicyProvider(new FeatureFlagPolicyProvider());
        }
    }
}
