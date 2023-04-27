<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\EventListener;

use Ibexa\Core\MVC\Symfony\Event\ScopeChangeEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use PlateShift\FeatureFlagBundle\API\FeatureFlagRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigurationScopeListener implements EventSubscriberInterface
{
    protected FeatureFlagRepository $featureFlagRepository;

    public function __construct(FeatureFlagRepository $featureFlagRepository)
    {
        $this->featureFlagRepository = $featureFlagRepository;
    }

    /**
     * Returns the subscribed events.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            MVCEvents::CONFIG_SCOPE_CHANGE => ['onConfigurationScopeChange', 100],
            MVCEvents::CONFIG_SCOPE_RESTORE => ['onConfigurationScopeChange', 100],
        ];
    }

    /**
     * Sets the scope to the feature flag repository.
     */
    public function onConfigurationScopeChange(ScopeChangeEvent $event): void
    {
        $this->featureFlagRepository->setSiteAccess($event->getSiteAccess());
    }
}
