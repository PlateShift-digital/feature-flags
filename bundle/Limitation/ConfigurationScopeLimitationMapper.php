<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\Limitation;

use Ibexa\AdminUi\Limitation\LimitationValueMapperInterface;
use Ibexa\AdminUi\Limitation\Mapper\MultipleSelectionBasedMapper;
use Ibexa\AdminUi\Translation\Extractor\LimitationTranslationExtractor;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;

class ConfigurationScopeLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface
{
    protected array $siteaccessList;

    protected array $siteaccessGroups;

    public function __construct(array $siteaccessList, array $siteaccessGroups)
    {
        $this->siteaccessList = $siteaccessList;
        $this->siteaccessGroups = $siteaccessGroups;
    }

    public function mapLimitationValue(Limitation $limitation): array
    {
        return $limitation->limitationValues;
    }

    protected function getSelectionChoices(): array
    {
        $groups = [];
        $list = [];
        $system = [];
        foreach (['global', 'default'] as $item) {
            $system['plateshift.feature_flag.scope.' . $item] = $item;
        }
        foreach ($this->siteaccessList as $item) {
            $list['plateshift.feature_flag.scope.' . $item] = $item;
        }
        foreach (array_keys($this->siteaccessGroups) as $item) {
            $groups['plateshift.feature_flag.scope.' . $item] = $item;
        }

        return [
        'plateshift.feature_flag.general' => $system,
        'plateshift.feature_flag.siteaccess.list' => $list,
        'plateshift.feature_flag.siteaccess.group.list' => $groups,
        ];
    }

    public function mapLimitationForm(FormInterface $form, Limitation $data): void
    {
        $options = $this->getChoiceFieldOptions() + [
            'multiple' => true,
            'label' => LimitationTranslationExtractor::identifierToLabel($data->getIdentifier()),
            'required' => false,
        ];
        $choices = $this->getSelectionChoices();
        $options += ['choices' => $choices];
        $options += ['translation_domain' => 'ibexa_repository_forms_policies'];
        $form->add('limitationValues', ChoiceType::class, $options);
    }
}
