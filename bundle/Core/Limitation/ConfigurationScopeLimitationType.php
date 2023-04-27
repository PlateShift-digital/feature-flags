<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\Core\Limitation;

use Ibexa\Contracts\Core\Limitation\Type as SPILimitationTypeInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation as APILimitationValue;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference as APIUserReference;
use Ibexa\Contracts\Core\Repository\Values\ValueObject as APIValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use PlateShift\FeatureFlagBundle\API\Repository\Values\User\Limitation\ConfigurationScopeLimitation;
use PlateShift\FeatureFlagBundle\Core\MVC\Symfony\ConfigurationScope;
use PlateShift\FeatureFlagBundle\Spi\Persistence\FeatureFlag;

class ConfigurationScopeLimitationType implements SPILimitationTypeInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function acceptValue(APILimitationValue $limitationValue): void
    {
        if (!$limitationValue instanceof ConfigurationScopeLimitation) {
            throw new InvalidArgumentType('$limitationValue', 'ConfigurationScopeLimitation', $limitationValue);
        }

        if (!is_array($limitationValue->limitationValues)) {
            throw new InvalidArgumentType('$limitationValue->limitationValues', 'array', $limitationValue->limitationValues);
        }

        foreach ($limitationValue->limitationValues as $key => $value) {
            // Value must be a CRC32, so can be either as string or integer.
            if (!is_string($value) && !is_int($value)) {
                throw new InvalidArgumentType("\$limitationValue->limitationValues[{$key}]", 'string or integer', $value);
            }
        }
    }

    public function validate(APILimitationValue $limitationValue): array
    {
        return [];
    }

    public function buildValue(array $limitationValues): ConfigurationScopeLimitation
    {
        return new ConfigurationScopeLimitation(['limitationValues' => $limitationValues]);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function evaluate(
        APILimitationValue $value,
        APIUserReference $currentUser,
        APIValueObject $object,
        array $targets = null
    ): bool {
        if (!$value instanceof ConfigurationScopeLimitation) {
            throw new InvalidArgumentException('$value', sprintf('Must be of type: %s, got "%s"', ConfigurationScopeLimitation::class, get_class($value)));
        }

        if ($object instanceof FeatureFlag) {
            $object = new ConfigurationScope($object->scope);
        } elseif (!$object instanceof ConfigurationScope) {
            throw new InvalidArgumentException('$object', sprintf('Must be of type: %s, got "%s"', ConfigurationScope::class, get_class($object)));
        }

        return
            !empty($value->limitationValues) &&
            in_array($object->name, $value->limitationValues, true)
        ;
    }

    /**
     * @throws NotImplementedException
     */
    public function getCriterion(APILimitationValue $value, APIUserReference $currentUser): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @throws NotImplementedException
     */
    public function valueSchema(): void
    {
        throw new NotImplementedException(__METHOD__);
    }
}
