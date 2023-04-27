<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\Core\Repository\Values;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * @property string         $identifier
 * @property string         $scope
 * @property string|null    $name
 * @property string|null    $description
 * @property array|string[] $groups
 * @property bool           $default
 * @property bool           $enabled
 */
class FeatureFlag extends ValueObject
{
    public array $checkedScopes;

    protected string $identifier;

    protected string $scope;

    protected ?string $name;

    protected ?string $description;

    protected array $groups;

    protected bool $default;

    protected bool $enabled;
}
