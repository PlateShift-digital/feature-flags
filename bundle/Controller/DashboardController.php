<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\Controller;

use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Core\MVC\Symfony\Security\Authorization\Attribute;
use PlateShift\FeatureFlagBundle\API\FeatureFlagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DashboardController extends AbstractController
{
    protected AuthorizationCheckerInterface $authorizationChecker;

    protected FeatureFlagRepository $featureFlagRepository;

    protected array $groupsBySiteaccess;

    protected array $featureDefinitions;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        FeatureFlagRepository $featureFlagRepository,
        array $groupsBySiteaccess,
        array $featureDefinitions
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->featureFlagRepository = $featureFlagRepository;
        $this->groupsBySiteaccess = $groupsBySiteaccess;
        $this->featureDefinitions = $featureDefinitions;
    }

    /**
     * @throws UnauthorizedException
     */
    public function dashboard(): Response
    {
        if (!$this->authorizationChecker->isGranted(new Attribute('plate_shift_feature_flag', 'dashboard'))) {
            throw new UnauthorizedException('plate_shift_feature_flag', 'dashboard');
        }

        return $this->render(
            '@ibexadesign/feature_flag/dashboard.html.twig',
            [
                'scopes' => $this->featureFlagRepository->getAllScopes(),
                'featureDefinitions' => $this->featureDefinitions,
            ]
        );
    }
}
