<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\Core\Persistence\Legacy;

use PlateShift\FeatureFlagBundle\Core\Persistence\Gateway;
use PlateShift\FeatureFlagBundle\Core\Repository\Values\FeatureFlag as FeatureValue;
use PlateShift\FeatureFlagBundle\Spi\Persistence\CreateStruct;
use PlateShift\FeatureFlagBundle\Spi\Persistence\FeatureFlag;
use PlateShift\FeatureFlagBundle\Spi\Persistence\Handler as SpiHandler;
use PlateShift\FeatureFlagBundle\Spi\Persistence\UpdateStruct;

class Handler implements SpiHandler
{
    protected Gateway $gateway;

    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function create(CreateStruct $struct): FeatureFlag
    {
        $this->gateway->insert($struct->identifier, $struct->scope, $struct->enabled);

        $row = $this->gateway->load($struct->identifier, $struct->scope);

        return $this->generateFeatureFromRow($row);
    }

    public function update(UpdateStruct $struct): FeatureFlag
    {
        $spiFeature = $struct->getFeature();
        $this->gateway->update($spiFeature->identifier, $spiFeature->scope, $struct->enabled);

        $row = $this->gateway->load($spiFeature->identifier, $spiFeature->scope);

        return $this->generateFeatureFromRow($row);
    }

    public function load(string $identifier, string $scope): ?FeatureFlag
    {
        $row = $this->gateway->load($identifier, $scope);

        if ($row) {
            return $this->generateFeatureFromRow($row);
        }

        return null;
    }

    public function delete(FeatureFlag $feature): void
    {
        $this->gateway->delete($feature->identifier, $feature->scope);
    }

    public function list(string $scope): array
    {
        $rows = $this->gateway->list($scope);
        $features = [];

        foreach ($rows as $row) {
            $features[] = $this->generateFeatureFromRow($row);
        }

        return $features;
    }

    protected function generateFeatureFromRow(array $row): FeatureFlag
    {
        return new FeatureFlag([
            'identifier' => $row[Gateway::COLUMN_IDENTIFIER],
            'scope'      => $row[Gateway::COLUMN_SCOPE],
            'enabled'    => (bool) $row[Gateway::COLUMN_ENABLED],
        ]);
    }
}
