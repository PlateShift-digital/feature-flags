<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\ResponseTagger\Value;

use Ibexa\HttpCache\ResponseTagger\Value\AbstractValueTagger;
use PlateShift\FeatureFlagBundle\Core\Repository\Values\FeatureFlag;

class FeatureFlagTagger extends AbstractValueTagger
{
    public function tag($value): self
    {
        if ($value instanceof FeatureFlag) {
            $this->responseTagger->addTags(array_map(
                static function (string $scope) use ($value) {
                    return 'ps-ff-' . $scope . '-' . $value->identifier;
                },
                $value->checkedScopes
            ));
        }

        return $this;
    }
}
