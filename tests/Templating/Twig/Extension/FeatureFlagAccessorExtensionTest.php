<?php

namespace Test\PlateShift\FeatureFlagBundle\Templating\Twig\Extension;

use PlateShift\FeatureFlagBundle\API\FeatureFlagRepository;
use PlateShift\FeatureFlagBundle\Core\Repository\Values\FeatureFlag;
use PlateShift\FeatureFlagBundle\Templating\Twig\Extension\FeatureFlagAccessorExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\TwigFunction;

/**
 * @covers \PlateShift\FeatureFlagBundle\Templating\Twig\Extension\FeatureFlagAccessorExtension
 */
class FeatureFlagAccessorExtensionTest extends TestCase
{
    public function test_all_template_functions_are_defined(): void
    {
        $repository = $this->createMock(FeatureFlagRepository::class);
        $extension = new FeatureFlagAccessorExtension($repository, $this->createMock(Environment::class));

        $functionNames = array_map(
            static function (TwigFunction $function) {
                return $function->getName();
            },
            $extension->getFunctions(),
        );

        $this->assertEquals(
            [
                'is_feature_enabled',
                'is_feature_disabled',
                'get_feature',
                'expose_features_json',
                'expose_features_data_attributes',
                'expose_features_javascript',
            ],
            $functionNames
        );
    }

    public function test_feature_enabled(): void
    {
        $repository = $this->createMock(FeatureFlagRepository::class);
        $repository->expects($this->once())->method('isEnabled')->with('identifier', 'scope')->willReturn(true);

        $extension = new FeatureFlagAccessorExtension($repository, $this->createMock(Environment::class));

        $this->assertTrue($extension->isFeatureEnabled('identifier', 'scope'));
    }

    public function test_feature_disabled(): void
    {
        $repository = $this->createMock(FeatureFlagRepository::class);
        $repository->expects($this->once())->method('isDisabled')->with('identifier', 'scope')->willReturn(true);

        $extension = new FeatureFlagAccessorExtension($repository, $this->createMock(Environment::class));

        $this->assertTrue($extension->isFeatureDisabled('identifier', 'scope'));
    }

    public function test_fetching_feature(): void
    {
        $feature = new FeatureFlag();

        $repository = $this->createMock(FeatureFlagRepository::class);
        $repository->expects($this->once())->method('get')->with('identifier', 'scope')->willReturn($feature);

        $extension = new FeatureFlagAccessorExtension($repository, $this->createMock(Environment::class));

        $this->assertEquals($feature, $extension->getFeature('identifier', 'scope'));
    }

    public function test_fetching_exposed_features_json(): void
    {
        $repository = $this->createMock(FeatureFlagRepository::class);
        $repository->expects($this->once())->method('getExposedFeatureStates')->with()->willReturn([
            'foo' => ['enabled' => false],
            'bar' => ['enabled' => true],
        ]);

        $extension = new FeatureFlagAccessorExtension($repository, $this->createMock(Environment::class));

        $this->assertEquals('{"foo":{"enabled":false},"bar":{"enabled":true}}', $extension->getFeaturesJson());
    }

    public function test_fetching_exposed_features_data_attributes(): void
    {
        $repository = $this->createMock(FeatureFlagRepository::class);
        $repository->expects($this->once())->method('getExposedFeatureStates')->with()->willReturn([
            'foo' => ['enabled' => false],
            'bar' => ['enabled' => true],
        ]);

        $extension = new FeatureFlagAccessorExtension($repository, $this->createMock(Environment::class));

        $this->assertEquals('data-foo="false" data-bar="true"', $extension->getFeaturesDataAttributes());
    }

    public function test_fetching_exposed_features_script_with_default_variable(): void
    {
        $repository = $this->createMock(FeatureFlagRepository::class);

        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())->method('render')
            ->with(
	            '@ibexadesign/feature_flag/expose/javascript.html.twig',
	            [
	                'variable' => 'psFeatureFlags',
	            ]
            )
            ->willReturn('the twig result');

        $extension = new FeatureFlagAccessorExtension($repository, $twig);

        $this->assertEquals('the twig result', $extension->getFeaturesJavaScript());
    }

    public function test_fetching_exposed_features_script_with_custom_variable(): void
    {
        $repository = $this->createMock(FeatureFlagRepository::class);

        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())->method('render')
            ->with(
	            '@ibexadesign/feature_flag/expose/javascript.html.twig',
	            [
	                'variable' => 'customVariable',
	            ]
            )
            ->willReturn('the twig result');

        $extension = new FeatureFlagAccessorExtension($repository, $twig);

        $this->assertEquals('the twig result', $extension->getFeaturesJavaScript('customVariable'));
    }
}
