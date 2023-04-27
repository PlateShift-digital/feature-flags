<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\API\Repository\Events\FeatureFlag;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;

class UpdatedStateEvent extends AfterEvent
{
    protected string $identifier;

    protected string $scope;

    public function __construct(string $identifier, string $scope)
    {
        $this->identifier = $identifier;
        $this->scope      = $scope;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getScope(): string
    {
        return $this->scope;
    }
}
