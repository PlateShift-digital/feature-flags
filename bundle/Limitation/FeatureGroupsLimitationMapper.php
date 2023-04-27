<?php

namespace PlateShift\FeatureFlagBundle\Limitation;

use Ibexa\AdminUi\Limitation\LimitationValueMapperInterface;
use Ibexa\AdminUi\Limitation\Mapper\MultipleSelectionBasedMapper;
use Ibexa\AdminUi\Translation\Extractor\LimitationTranslationExtractor;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;

class FeatureGroupsLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface
{
    private array $featureGroups;

    public function __construct(array $featureGroups)
    {
        $this->featureGroups = $featureGroups;
    }

    public function mapLimitationValue(Limitation $limitation): array
    {
        return $limitation->limitationValues;
    }

    protected function getSelectionChoices(): array
    {
        $groups = [];

        foreach ($this->featureGroups as $item) {
            $groups['plateshift.feature_group.scope.' . $item] = $item;
        }

        asort($groups);

        return $groups;
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
