<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\Templating\Twig\Extension;

use PlateShift\FeatureFlagBundle\API\FeatureFlagRepository;
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
                function (string $identifier, string $scope = null) {
                    return $this->featureFlagRepository->isEnabled($identifier, $scope);
                },
                ['needs_environment' => false]
            ),
            new TwigFunction(
                'is_feature_disabled',
                function (string $identifier, string $scope = null) {
                    return $this->featureFlagRepository->isDisabled($identifier, $scope);
                },
                ['needs_environment' => false]
            ),
            new TwigFunction(
                'get_feature',
                function (string $identifier, string $scope = null) {
                    return $this->featureFlagRepository->get($identifier, $scope);
                },
                ['needs_environment' => false]
            ),
            new TwigFunction(
                'expose_features_json',
                function () {
                    return json_encode(
                        $this->featureFlagRepository->getExposedFeatureStates(),
                        JSON_THROW_ON_ERROR,
                        2
                    );
                },
                ['needs_environment' => false, 'is_safe' => ['html']]
            ),
            new TwigFunction(
                'expose_features_data_attributes',
                function () {
                    $features = [];
                    foreach ($this->featureFlagRepository->getExposedFeatureStates() as $identifier => $feature) {
                        $features[] = sprintf(
                            'data-%s="%s"',
                            str_replace('_', '-', $identifier),
                            $feature['enabled'] ? 'true' : 'false'
                        );
                    }

                    return implode(' ', $features);
                },
                ['needs_environment' => false, 'is_safe' => ['html']]
            ),
            new TwigFunction(
                'expose_features_javascript',
                function (string $variable = 'psFeatureFlags') {
                    return $this->environment->render(
                        '@ibexadesign/feature_flag/expose/javascript.html.twig',
                        [
                            'variable' => $variable,
                        ]
                    );
                },
                ['needs_environment' => false, 'is_safe' => ['html']]
            ),
        ];
    }
}
