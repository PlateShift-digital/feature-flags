<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\Core\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\BadStateException;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException as ApiInvalidArgumentException;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use PlateShift\FeatureFlagBundle\API\Repository\FeatureFlagService as ApiFeatureFlagService;
use PlateShift\FeatureFlagBundle\Core\Repository\Values\FeatureFlag;
use PlateShift\FeatureFlagBundle\Spi\Persistence\CreateStruct;
use PlateShift\FeatureFlagBundle\Spi\Persistence\FeatureFlag as SpiFeature;
use PlateShift\FeatureFlagBundle\Spi\Persistence\Handler;
use PlateShift\FeatureFlagBundle\Spi\Persistence\UpdateStruct;
use Symfony\Contracts\Translation\TranslatorInterface;

class FeatureFlagService implements ApiFeatureFlagService
{
    protected Handler $handler;

    protected TranslatorInterface $translator;

    protected PermissionResolver $permissionResolver;

    protected array $featureDefinitions;

    public function __construct(
        Handler $handler,
        TranslatorInterface $translator,
        PermissionResolver $permissionResolver,
        array $featureDefinitions
    ) {
        $this->handler = $handler;
        $this->permissionResolver = $permissionResolver;
        $this->translator = $translator;
        $this->featureDefinitions = $featureDefinitions;
    }

    /**
     * @throws NotFoundException
     */
    public function load(string $identifier, string $scope): FeatureFlag
    {
        $spiFeature = $this->handler->load($identifier, $scope);

        if (!$spiFeature) {
            throw new NotFoundException('FeatureFlag', compact('identifier', 'scope'));
        }

        return $this->generateFeatureFromSpi($spiFeature);
    }

    public function list(string $scope): array
    {
        $features = [];

        foreach ($this->handler->list($scope) as $spiFeature) {
            try {
                $features[$spiFeature->identifier] = $this->generateFeatureFromSpi($spiFeature);
            } catch (NotFoundException) {
                continue;
            }
        }

        return $features;
    }

    public function newFeatureFlagCreateStruct(): CreateStruct
    {
        return new CreateStruct();
    }

    /**
     * @throws BadStateException|InvalidArgumentException|NotFoundException|UnauthorizedException|ApiInvalidArgumentException
     */
    public function create(CreateStruct $createStruct): FeatureFlag
    {
        $spiFeature = new SpiFeature([
            'identifier' => $createStruct->identifier,
            'scope' => $createStruct->scope,
        ]);

        if (!$this->permissionResolver->canUser('plate_shift_feature_flag', 'change', $spiFeature)) {
            throw new UnauthorizedException('plate_shift_feature_flag', 'change', ['feature' => $spiFeature->identifier, 'scope' => $spiFeature->scope]);
        }

        if (!isset($this->featureDefinitions[$createStruct->identifier])) {
            throw new InvalidArgumentException('$createStruct->identifier', sprintf('expected one of "%s"', implode('", "', array_keys($this->featureDefinitions))));
        }

        $spiFeature = $this->handler->create($createStruct);

        return $this->generateFeatureFromSpi($spiFeature);
    }

    public function newFeatureFlagUpdateStruct(FeatureFlag $feature): UpdateStruct
    {
        return new UpdateStruct(new SpiFeature([
            'identifier' => $feature->identifier,
            'scope' => $feature->scope,
            'enabled' => $feature->enabled,
        ]));
    }

    /**
     * @throws BadStateException|NotFoundException|UnauthorizedException|ApiInvalidArgumentException
     */
    public function update(UpdateStruct $updateStruct): FeatureFlag
    {
        $feature = $updateStruct->getFeature();

        if (!$this->permissionResolver->canUser('plate_shift_feature_flag', 'change', $feature)) {
            throw new UnauthorizedException('plate_shift_feature_flag', 'change', ['feature' => $feature->identifier, 'scope' => $feature->scope]);
        }

        $spiFeature = $this->handler->update($updateStruct);

        return $this->generateFeatureFromSpi($spiFeature);
    }

    /**
     * @throws UnauthorizedException|BadStateException|ApiInvalidArgumentException
     */
    public function delete(FeatureFlag $feature): void
    {
        $spiFeature = new SpiFeature([
            'identifier' => $feature->identifier,
            'scope' => $feature->scope,
        ]);

        if (!$this->permissionResolver->canUser('plate_shift_feature_flag', 'change', $spiFeature)) {
            throw new UnauthorizedException('plate_shift_feature_flag', 'change', ['feature' => $spiFeature->identifier, 'scope' => $spiFeature->scope]);
        }

        $this->handler->delete($spiFeature);
    }

    /**
     * @throws NotFoundException
     */
    private function generateFeatureFromSpi(SpiFeature $feature): FeatureFlag
    {
        $featureDefinition = $this->getFeatureDefinition($feature->identifier);

        if (!$featureDefinition) {
            throw new NotFoundException('FeatureDefinition', ['identifier' => $feature->identifier]);
        }

        return new FeatureFlag([
            'identifier' => $feature->identifier,
            'scope' => $feature->scope,
            'name' => $this->translate($featureDefinition['name']),
            'description' => $this->translate($featureDefinition['description']),
            'groups' => $featureDefinition['groups'],
            'default' => $featureDefinition['default'],
            'enabled' => $feature->enabled,
        ]);
    }

    private function translate(array $part)
    {
        if ($part['context']) {
            return $this->translator->trans($part['id'], [], $part['context']);
        }

        return $part['id'];
    }

    private function getFeatureDefinition(string $identifier): ?array
    {
        return $this->featureDefinitions[$identifier] ?? null;
    }
}
