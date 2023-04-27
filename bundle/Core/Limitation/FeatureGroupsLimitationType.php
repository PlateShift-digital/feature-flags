<?php

namespace PlateShift\FeatureFlagBundle\Core\Limitation;

use Ibexa\Contracts\Core\Limitation\Type as SPILimitationTypeInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation as APILimitationValue;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference as APIUserReference;
use Ibexa\Contracts\Core\Repository\Values\ValueObject as APIValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use PlateShift\FeatureFlagBundle\API\Repository\Values\User\Limitation\FeatureGroupsLimitation;
use PlateShift\FeatureFlagBundle\Core\MVC\Symfony\FeatureGroups;
use PlateShift\FeatureFlagBundle\Spi\Persistence\FeatureFlag;

class FeatureGroupsLimitationType implements SPILimitationTypeInterface
{
    public array $featureDefinitions;

    public function __construct(array $featureDefinitions)
    {
        $this->featureDefinitions = $featureDefinitions;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function acceptValue(APILimitationValue $limitationValue): void
    {
        if (!$limitationValue instanceof FeatureGroupsLimitation) {
            throw new InvalidArgumentType('$limitationValue', 'FeatureGroupsLimitation', $limitationValue);
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

    public function buildValue(array $limitationValues): FeatureGroupsLimitation
    {
        return new FeatureGroupsLimitation(['limitationValues' => $limitationValues]);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function evaluate(
        APILimitationValue $value,
        APIUserReference $currentUser,
        APIValueObject $object,
        array $targets = null
    ): ?bool {
        if (!$value instanceof FeatureGroupsLimitation) {
            throw new InvalidArgumentException('$value', 'Must be of type: FeatureGroupsLimitation');
        }

        if ($object instanceof FeatureFlag) {
            $object = new FeatureGroups($this->featureDefinitions[$object->identifier]['groups']);
        } elseif (!$object instanceof FeatureGroups) {
            throw new InvalidArgumentException('$object', sprintf('Must be of type: %s or %s. %s given', FeatureFlag::class, FeatureGroups::class, get_class($object)));
        }

        $anyMatches = false;
        foreach ($value->limitationValues as $limitationValue) {
            if (in_array($limitationValue, $object->groups, true)) {
                $anyMatches = true;

                break;
            }
        }

        return !empty($value->limitationValues) && $anyMatches;
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
