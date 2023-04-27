<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

class PlateShiftFeatureFlagExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            'plateshift.feature.flag.allow_cookie_manipulation',
            $config['allow_cookie_manipulation'] ?? false
        );

        $featureGroups = [];
        $features = $config['features'] ?? [];
        foreach ($features as $identifier => $feature) {
            $features[$identifier]['identifier'] = $identifier;

            $featureGroups = array_unique(array_merge($featureGroups, $features[$identifier]['groups']));
        }
        $container->setParameter('plateshift.feature.flag.feature_list', $features);
        $container->setParameter('plateshift.feature.flag.feature_groups', $featureGroups);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('controller.yaml');
        $loader->load('persistence.yaml');
        $loader->load('event_listeners.yaml');
        $loader->load('services.yaml');
        $loader->load('templating.yaml');
        $loader->load('role.yaml');
        $loader->load('view_cache.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $systemConfigFiles = [
            'ibexa',
            'ibexa_design_engine',
        ];

        foreach ($systemConfigFiles as $configFileName) {
            $configFile = __DIR__ . '/../Resources/config/' . $configFileName . '.yaml';
            $config = Yaml::parse(file_get_contents($configFile));
            $container->prependExtensionConfig($configFileName, $config);
            $container->addResource(new FileResource($configFile));
        }

        $container->prependExtensionConfig(
            'bazinga_js_translation',
            [
                'active_domains' => [
                    'feature_flag',
                ],
            ]
        );
    }
}
