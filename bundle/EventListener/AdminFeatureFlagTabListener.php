<?php

namespace PlateShift\FeatureFlagBundle\EventListener;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\AdminUi\Menu\MainMenuBuilder;
use Ibexa\Core\MVC\Symfony\Security\Authorization\Attribute;
use JetBrains\PhpStorm\ArrayShape;
use Knp\Menu\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminFeatureFlagTabListener implements EventSubscriberInterface
{
    public const ITEM__MIGRATION = 'main__admin__feature_flag';

    protected AuthorizationCheckerInterface $authorizationChecker;

    protected TranslatorInterface $translator;

    protected FactoryInterface $factory;

    #[ArrayShape([
        ConfigureMenuEvent::MAIN_MENU => 'string',
    ])]
    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMenuEvent::MAIN_MENU => 'onMenuConfigure',
        ];
    }

    public function setAuthorizationChecker(AuthorizationCheckerInterface $authorizationChecker): void
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    public function setFactory(FactoryInterface $factory): void
    {
        $this->factory = $factory;
    }

    public function onMenuConfigure(ConfigureMenuEvent $event): void
    {
        if ($this->authorizationChecker->isGranted(new Attribute('plate_shift_feature_flag', 'change'))) {
            $adminMenu = $event->getMenu()->getChild(MainMenuBuilder::ITEM_ADMIN);

            $adminMenu?->addChild(
                $this->factory->createItem(
                    self::ITEM__MIGRATION,
                    [
                        'label' => $this->translator->trans('menu.button.text', [], 'feature_flag'),
                        'route' => 'plateshift_featureFlag_dashboard',
                        'routeParameters' => [],
                    ]
                )
            );
        }
    }
}
