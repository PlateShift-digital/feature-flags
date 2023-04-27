<?php

namespace PlateShift\FeatureFlagBundle\Controller;

use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Core\MVC\Symfony\Security\Authorization\Attribute;
use JsonException;
use PlateShift\FeatureFlagBundle\API\FeatureFlagRepository;
use PlateShift\FeatureFlagBundle\API\Repository\FeatureFlagService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class FeatureFlagOperationController extends AbstractController
{
    protected AuthorizationCheckerInterface $authorizationChecker;

    protected FeatureFlagRepository $featureFlagRepository;

    protected FeatureFlagService $featureFlagService;

    protected array $featureDefinitions;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        FeatureFlagRepository $featureFlagRepository,
        FeatureFlagService $featureFlagService,
        array $featureDefinitions
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->featureFlagRepository = $featureFlagRepository;
        $this->featureFlagService = $featureFlagService;
        $this->featureDefinitions = $featureDefinitions;
    }

    /**
     * @throws UnauthorizedException
     */
    public function list(string $scope): JsonResponse
    {
        if (!$this->authorizationChecker->isGranted(new Attribute('plate_shift_feature_flag', 'dashboard'))) {
            throw new UnauthorizedException('plate_shift_feature_flag', 'dashboard');
        }

        $featureList = [];

        foreach ($this->featureDefinitions as $featureDefinition) {
            $identifier = $featureDefinition['identifier'];
            $featureFlag = $this->featureFlagRepository->get($identifier, $scope);

            try {
                $enabled = $this->featureFlagService->load($identifier, $scope)->enabled;
            } catch (NotFoundException) {
                $enabled = null;
            }

            $featureList[] = [
                'identifier' => $identifier,
                'name' => $featureFlag->name,
                'description' => $featureFlag->description,
                'default' => $featureFlag->default,
                'enabled' => $enabled,
                'scope' => $scope,
                'fromEnabled' => $featureFlag->enabled,
                'fromScope' => $featureFlag->scope,
            ];
        }

        return new JsonResponse($featureList);
    }

    /**
     * @throws JsonException
     */
    public function change(Request $request): Response
    {
        $target = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        try {
            $featureFlag = $this->featureFlagService->load($target['identifier'], $target['scope']);
            $updateStruct = $this->featureFlagService->newFeatureFlagUpdateStruct($featureFlag);
            $updateStruct->enabled = $target['state'];

            $featureFlag = $this->featureFlagService->update($updateStruct);
        } catch (NotFoundException) {
            $createStruct = $this->featureFlagService->newFeatureFlagCreateStruct();
            $createStruct->scope = $target['scope'];
            $createStruct->identifier = $target['identifier'];
            $createStruct->enabled = $target['state'];

            $featureFlag = $this->featureFlagService->create($createStruct);
        }

        return new JsonResponse([
            'message' => sprintf(
                'Successfully %s feature "%s".',
                $featureFlag->enabled ? 'enabled' : 'disabled',
                $featureFlag->name
            ),
        ]);
    }

    /**
     * @throws JsonException|NotFoundException
     */
    public function reset(Request $request): Response
    {
        $target = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $featureFlag = $this->featureFlagService->load($target['identifier'], $target['scope']);
        $this->featureFlagService->delete($featureFlag);

        return new JsonResponse([
            'message' => sprintf('Successfully reset feature "%s".', $featureFlag->name),
        ]);
    }
}
