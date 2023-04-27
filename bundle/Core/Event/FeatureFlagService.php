<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\Core\Event;

use PlateShift\FeatureFlagBundle\API\Repository\Events\FeatureFlag\CreatedStateEvent;
use PlateShift\FeatureFlagBundle\API\Repository\Events\FeatureFlag\RemovedStateEvent;
use PlateShift\FeatureFlagBundle\API\Repository\Events\FeatureFlag\UpdatedStateEvent;
use PlateShift\FeatureFlagBundle\API\Repository\FeatureFlagService as FeatureFlagServiceInterface;
use PlateShift\FeatureFlagBundle\Core\Repository\Values\FeatureFlag;
use PlateShift\FeatureFlagBundle\Spi\Persistence\CreateStruct;
use PlateShift\FeatureFlagBundle\Spi\Persistence\UpdateStruct;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class FeatureFlagService implements FeatureFlagServiceInterface
{
    protected FeatureFlagServiceInterface $service;

    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(FeatureFlagServiceInterface $service, EventDispatcherInterface $eventDispatcher)
    {
        $this->service         = $service;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function load(string $identifier, string $scope): FeatureFlag
    {
        return $this->service->load($identifier, $scope);
    }

    public function list(string $scope): array
    {
        return $this->service->list($scope);
    }

    public function newFeatureFlagCreateStruct(): CreateStruct
    {
        return $this->service->newFeatureFlagCreateStruct();
    }

    public function create(CreateStruct $createStruct): FeatureFlag
    {
        $featureFlag = $this->service->create($createStruct);

        $this->eventDispatcher->dispatch(new CreatedStateEvent($featureFlag->identifier, $featureFlag->scope));

        return $featureFlag;
    }

    public function newFeatureFlagUpdateStruct(FeatureFlag $feature): UpdateStruct
    {
        return $this->service->newFeatureFlagUpdateStruct($feature);
    }

    public function update(UpdateStruct $updateStruct): FeatureFlag
    {
        $featureFlag = $this->service->update($updateStruct);

        $this->eventDispatcher->dispatch(new UpdatedStateEvent(
            $updateStruct->getFeature()->identifier, $updateStruct->getFeature()->scope
        ));

        return $featureFlag;
    }

    public function delete(FeatureFlag $feature): void
    {
        $this->service->delete($feature);

        $this->eventDispatcher->dispatch(new RemovedStateEvent($feature->identifier, $feature->scope));
    }
}
