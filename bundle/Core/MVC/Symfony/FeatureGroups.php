<?php

namespace PlateShift\FeatureFlagBundle\Core\MVC\Symfony;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class FeatureGroups extends ValueObject
{
    public array $groups;

    public function __construct(array $groups)
    {
        parent::__construct([
            'groups' => $groups,
        ]);
    }
}
