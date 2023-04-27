<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\Services;

use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessAware;
use Ibexa\HttpCache\ResponseTagger\Delegator\DispatcherTagger;
use function in_array;
use PlateShift\FeatureFlagBundle\API\FeatureFlagRepository as ApiFeatureFlagRepository;
use PlateShift\FeatureFlagBundle\API\Repository\FeatureFlagService;
use PlateShift\FeatureFlagBundle\Core\Repository\Values\FeatureFlag;
use Symfony\Contracts\Translation\TranslatorInterface;

class FeatureFlagRepository implements ApiFeatureFlagRepository, SiteAccessAware
{
    protected FeatureFlagService $featureFlagService;

    protected TranslatorInterface $translator;

    protected DispatcherTagger $tagger;

    protected array $groupsBySiteaccess;

    protected array $featureDefinitions;

    protected array $siteaccessList;

    protected SiteAccess $siteaccess;

    protected array $featureFlagsByScope = ['_temp_' => []];

    protected bool $debugEnabled;

    public function __construct(
        FeatureFlagService $featureFlagService,
        TranslatorInterface $translator,
        DispatcherTagger $tagger,
        array $featureDefinitions,
        array $siteaccessList,
        array $groupsBySiteaccess,
        bool $debugEnabled
    ) {
        $this->featureFlagService = $featureFlagService;
        $this->translator = $translator;
        $this->tagger = $tagger;
        $this->featureDefinitions = $featureDefinitions;
        $this->siteaccessList = $siteaccessList;
        $this->groupsBySiteaccess = $groupsBySiteaccess;
        $this->debugEnabled = $debugEnabled;
    }

    public function setSiteAccess(SiteAccess $siteAccess = null): void
    {
        if (null !== $siteAccess) {
            $this->siteaccess = $siteAccess;
        }
    }

    public function isEnabled(string $identifier, string $scope = null): bool
    {
        return $this->get($identifier, $scope)->enabled;
    }

    public function isDisabled(string $identifier, string $scope = null): bool
    {
        return !$this->isEnabled($identifier, $scope);
    }

    /**
     * @internal this method does not notify the cache and should not be used for cache sensitive resources
     */
    public function get(string $identifier, string $scope = null): FeatureFlag
    {
        $weightedScopes = $this->getWeightedActiveScopes($scope);

        foreach ($weightedScopes as $weightedActiveScope) {
            if (!isset($this->featureFlagsByScope[$weightedActiveScope])) {
                if ('_definition_' === $weightedActiveScope) { // _definition_ is not a valid scope (_temp_ is always defined)
                    continue;
                }

                $this->featureFlagsByScope[$weightedActiveScope] = $this->featureFlagService->list($weightedActiveScope);
            }
        }

        if (!isset($this->featureFlagsByScope['_definition_'])) {
            $this->featureFlagsByScope['_definition_'] = array_map(
                function ($featureDefinition) {
                    return new FeatureFlag([
                        'identifier' => $featureDefinition['identifier'],
                        'scope' => '_definition_',
                        'name' => $this->translate($featureDefinition['name']),
                        'description' => $this->translate($featureDefinition['description']),
                        'default' => $featureDefinition['default'],
                        'enabled' => $featureDefinition['default'],
                    ]);
                },
                $this->featureDefinitions
            );
        }

        $checkedScopes = [];
        foreach ($weightedScopes as $weightedActiveScope) {
            if (!in_array($weightedActiveScope, ['_temp_', '_definition_'])) {
                $checkedScopes[] = $weightedActiveScope;
            }

            if (isset($this->featureFlagsByScope[$weightedActiveScope][$identifier])) {
                $result = $this->featureFlagsByScope[$weightedActiveScope][$identifier];

                break;
            }
        }

        if (!isset($result)) {
            if ($this->debugEnabled) {
                trigger_error(
                    sprintf('Feature with identifier "%s" is in neither storage, cache or definition!', $identifier),
                    E_USER_WARNING
                );
            }

            $result = new FeatureFlag([
                'identifier' => $identifier,
                'scope' => '_not_found_',
                'default' => false,
                'enabled' => false,
            ]);
        }

        $result->checkedScopes = $checkedScopes;

        $this->tagger->tag($result);

        return $result;
    }

    public function enableFeature(string $identifier): void
    {
        $this->featureFlagsByScope['_temp_'][$identifier] = new FeatureFlag([
            'identifier' => $identifier,
            'scope' => '_temp_',
            'enabled' => true,
        ]);
    }

    public function disableFeature(string $identifier): void
    {
        $this->featureFlagsByScope['_temp_'][$identifier] = new FeatureFlag([
            'identifier' => $identifier,
            'scope' => '_temp_',
            'enabled' => false,
        ]);
    }

    public function reset(): void
    {
        $this->featureFlagsByScope['_temp_'] = [];
    }

    public function getExposedFeatureStates(): array
    {
        $features = [];

        foreach ($this->featureDefinitions as $identifier => $featureDefinition) {
            if ($featureDefinition['exposed'] ?? false) {
                $features[$identifier] = [
                    'enabled' => $this->isEnabled($identifier),
                ];
            }
        }

        return $features;
    }

    public function getAllScopes(): array
    {
        return array_merge(
            ['global'],
            array_unique($this->siteaccessList),
            array_reduce(
                $this->groupsBySiteaccess,
                static function ($carry, $groupList) {
                    foreach ($groupList as $group) {
                        if (!in_array($group, $carry, true)) {
                            $carry[] = $group;
                        }
                    }

                    return $carry;
                },
                []
            ),
            ['default']
        );
    }

    private function getWeightedActiveScopes(?string $scope = null): array
    {
        if (null === $scope) {
            $scope = $this->siteaccess->name;
        }

        $scopes = [
            '_temp_',
            'global',
            $scope,
        ];

        foreach ($this->groupsBySiteaccess[$scope] ?? [] as $siteaccessGroup) {
            $scopes[] = $siteaccessGroup;
        }

        $scopes[] = 'default';
        $scopes[] = '_definition_';

        return $scopes;
    }

    private function translate(array $part)
    {
        if ($part['context']) {
            return $this->translator->trans($part['id'], [], $part['context']);
        }

        return $part['id'];
    }
}
