<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\EventListener;

use Ibexa\Contracts\HttpCache\PurgeClient\PurgeClientInterface;
use PlateShift\FeatureFlagBundle\API\Repository\Events\FeatureFlag\CreatedStateEvent;
use PlateShift\FeatureFlagBundle\API\Repository\Events\FeatureFlag\RemovedStateEvent;
use PlateShift\FeatureFlagBundle\API\Repository\Events\FeatureFlag\UpdatedStateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FeatureFlagEventsSubscriber implements EventSubscriberInterface
{
    protected PurgeClientInterface $purgeClient;

    public function __construct(PurgeClientInterface $purgeClient)
    {
        $this->purgeClient = $purgeClient;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CreatedStateEvent::class => 'onFeatureFlagStateCreated',
            RemovedStateEvent::class => 'onFeatureFlagStateRemoved',
            UpdatedStateEvent::class => 'onFeatureFlagStateUpdated',
        ];
    }

    public function onFeatureFlagStateCreated(CreatedStateEvent $event): void
    {
        $this->purgeClient->purge(['ps-ff-' . $event->getScope() . '-' . $event->getIdentifier()]);
    }

    public function onFeatureFlagStateRemoved(RemovedStateEvent $event): void
    {
        $this->purgeClient->purge(['ps-ff-' . $event->getScope() . '-' . $event->getIdentifier()]);
    }

    public function onFeatureFlagStateUpdated(UpdatedStateEvent $event): void
    {
        $this->purgeClient->purge(['ps-ff-' . $event->getScope() . '-' . $event->getIdentifier()]);
    }
}
