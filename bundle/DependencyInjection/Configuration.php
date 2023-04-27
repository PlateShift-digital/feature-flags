<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('plate_shift_feature_flag');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode->children()
            ->booleanNode('allow_cookie_manipulation')
                ->info('Allow users to set a cookie to enable features (check docs for varnish support!).')
                ->defaultValue(false)
            ->end();

        /** @var ArrayNodeDefinition $featuresNode */
        $featuresNode = $rootNode->children()
            ->arrayNode('features')
                ->validate()
                    ->ifTrue(static function ($array) {
                        $identifiers = array_keys($array);

                        foreach ($identifiers as $identifier) {
                            if (!preg_match('/^[a-z0-9_]+$/', $identifier)) {
                                return true;
                            }
                        }

                        return false;
                    })
                    ->thenInvalid('Feature keys may only consist of lowercase letters, numbers and underscores.')
                ->end()
                ->prototype('array');

        $this->addTranslatableString($featuresNode, 'name');
        $this->addTranslatableString($featuresNode, 'description');

        $children = $featuresNode->children();

        $children->arrayNode('groups')
            ->info('The groups of the feature used for policy limitations.')
            ->defaultValue([])
            ->prototype('scalar');
        $children->booleanNode('exposed')
            ->info('Sets the feature to be exposed to the frontend.')
            ->defaultFalse();
        $children->booleanNode('default')
            ->info('The default state of the feature.')
            ->defaultFalse();

        return $treeBuilder;
    }

    private function addTranslatableString(ArrayNodeDefinition $arrayNode, string $key): void
    {
        $subNodeDefinition = $arrayNode
            ->children()
            ->arrayNode($key);

        $nodeBuilder = $subNodeDefinition
            ->info('Can contain a static string or id and context to support multiple languages.')
            ->children();

        $nodeBuilder->scalarNode('id');
        $nodeBuilder->scalarNode('context');

        $subNodeDefinition
            ->beforeNormalization()
                ->ifString()
                    ->then(static function (string $string) {
                        return [
                            'id' => $string,
                            'context' => null,
                        ];
                    });
    }
}
