<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\Templating\Twig\Extension;

use PlateShift\FeatureFlagBundle\API\FeatureFlagRepository;
use PlateShift\FeatureFlagBundle\Core\Repository\Values\FeatureFlag;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FeatureFlagAccessorExtension extends AbstractExtension
{
    protected FeatureFlagRepository $featureFlagRepository;

    protected Environment $environment;

    public function __construct(FeatureFlagRepository $featureFlagRepository, Environment $environment)
    {
        $this->featureFlagRepository = $featureFlagRepository;
        $this->environment           = $environment;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'is_feature_enabled',
                [$this, 'isFeatureEnabled'],
                ['needs_environment' => false]
            ),
            new TwigFunction(
                'is_feature_disabled',
                [$this, 'isFeatureDisabled'],
                ['needs_environment' => false]
            ),
            new TwigFunction(
                'get_feature',
                [$this, 'getFeature'],
                ['needs_environment' => false]
            ),
            new TwigFunction(
                'expose_features_json',
                [$this, 'getFeaturesJson'],
                ['needs_environment' => false, 'is_safe' => ['html']]
            ),
            new TwigFunction(
                'expose_features_data_attributes',
                [$this, 'getFeaturesDataAttributes'],
                ['needs_environment' => false, 'is_safe' => ['html']]),
            new TwigFunction(
                'expose_features_javascript',
                [$this, 'getFeaturesJavaScript'],
                ['needs_environment' => false, 'is_safe' => ['html']]
            ),
        ];
    }

    public function isFeatureEnabled(string $identifier, string $scope = null): bool
    {
        return $this->featureFlagRepository->isEnabled($identifier, $scope);
    }

    public function isFeatureDisabled(string $identifier, string $scope = null): bool
    {
        return $this->featureFlagRepository->isDisabled($identifier, $scope);
    }

    public function getFeature(string $identifier, string $scope = null): FeatureFlag
    {
        return $this->featureFlagRepository->get($identifier, $scope);
    }

    public function getFeaturesJson(): string
    {
        return json_encode(
            $this->featureFlagRepository->getExposedFeatureStates(),
            JSON_THROW_ON_ERROR,
            2
        );
    }

    public function getFeaturesDataAttributes(): string
    {
        $features = [];

        foreach ($this->featureFlagRepository->getExposedFeatureStates() as $identifier => $feature) {
            $features[] = sprintf(
                'data-%s="%s"',
                str_replace('_', '-', $identifier),
                $feature['enabled'] ? 'true' : 'false'
            );
        }

        return implode(' ', $features);
    }

    public function getFeaturesJavaScript(string $variable = 'psFeatureFlags'): string
    {
        return $this->environment->render(
            '@ibexadesign/feature_flag/expose/javascript.html.twig',
            [
                'variable' => $variable,
            ]
        );
    }
}
