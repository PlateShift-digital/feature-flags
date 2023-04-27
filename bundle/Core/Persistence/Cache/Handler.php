<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\Core\Persistence\Cache;

use PlateShift\FeatureFlagBundle\Spi\Persistence\CreateStruct;
use PlateShift\FeatureFlagBundle\Spi\Persistence\FeatureFlag;
use PlateShift\FeatureFlagBundle\Spi\Persistence\Handler as SpiHandler;
use PlateShift\FeatureFlagBundle\Spi\Persistence\UpdateStruct;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

class Handler implements SpiHandler
{
    protected SpiHandler $handler;

    protected TagAwareAdapterInterface $cache;

    public function __construct(SpiHandler $handler, TagAwareAdapterInterface $cache)
    {
        $this->handler = $handler;
        $this->cache = $cache;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function create(CreateStruct $struct): FeatureFlag
    {
        $feature = $this->handler->create($struct);

        $cacheItem = $this->cache->getItem($this->getCacheKey($struct->scope, $struct->identifier));
        $cacheItem->set($feature);

        $this->cache->save($cacheItem);
        $this->cache->deleteItem($this->getCacheKey($feature->scope));

        return $feature;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function update(UpdateStruct $struct): FeatureFlag
    {
        $feature = $this->handler->update($struct);
        $cacheKey = $this->getCacheKey($struct->getFeature()->scope, $struct->getFeature()->identifier);

        $cacheItem = $this->cache->getItem($cacheKey);
        $cacheItem->set($feature);

        $this->cache->save($cacheItem);
        $this->cache->deleteItem($this->getCacheKey($feature->scope));

        return $feature;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function load(string $identifier, string $scope): ?FeatureFlag
    {
        $cacheItem = $this->cache->getItem($this->getCacheKey($scope, $identifier));

        if ($cacheItem->isHit() && $cacheItem->get() instanceof FeatureFlag) {
            return $cacheItem->get();
        }

        $feature = $this->handler->load($identifier, $scope);

        if ($feature instanceof FeatureFlag) {
            $cacheItem->set($feature);
            $this->cache->save($cacheItem);
        }

        return $feature;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function delete(FeatureFlag $feature): void
    {
        $this->cache->deleteItems([
            $this->getCacheKey($feature->scope),
            $this->getCacheKey($feature->scope, $feature->identifier),
        ]);

        $this->handler->delete($feature);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function list(string $scope): array
    {
        $cacheItem = $this->cache->getItem($this->getCacheKey($scope));

        if ($cacheItem->isHit() && $cacheItem->get() instanceof FeatureFlag) {
            return $cacheItem->get();
        }

        $featureList = $this->handler->list($scope);

        $cacheItem->set($featureList);
        $this->cache->save($cacheItem);

        return $featureList;
    }

    protected function getCacheKey(string $scope, string $identifier = null): string
    {
        if (!$identifier) {
            $identifier = '--list';
        }

        return sprintf('ps-ff-%s-%s', $scope, $identifier);
    }
}
