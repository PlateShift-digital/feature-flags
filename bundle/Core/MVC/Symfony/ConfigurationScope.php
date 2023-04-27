<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\Core\MVC\Symfony;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class ConfigurationScope extends ValueObject
{
    public string $name;

    public function __construct(string $name)
    {
        parent::__construct([
            'name' => $name,
        ]);
    }
}
