<?php

namespace App\Controller;

use App\Api\CompetitionViewFactory;
use App\Entity\User;
use App\Repository\CompetitionRepository;
use App\Repository\RegionRepository;
use App\Repository\SportRepository;
use App\Service\CompetitionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CompetitionController extends AbstractController
{
    #[Route('/api/competitions', name: 'api_competitions_index', methods: ['GET'])]
    public function index(
        Request $request,
        SportRepository $sportRepository,
        CompetitionRepository $competitionRepository,
        CompetitionViewFactory $competitionViewFactory,
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['message' => 'Authentication required.'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $sportId = $request->query->get('sport');

        if (!is_string($sportId) || '' === trim($sportId) || !ctype_digit($sportId)) {
            return $this->json(['message' => 'Valid sport query parameter is required.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $sport = $sportRepository->findOneBy([
            'id' => (int) $sportId,
            'isActive' => true,
        ]);

        if (null === $sport) {
            return $this->json(['message' => 'Sport not found.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $competitions = array_map(
            $competitionViewFactory->create(...),
            $competitionRepository->findActiveVisibleForUserAndSport($user, $sport)
        );

        return $this->json($competitions);
    }

    #[Route('/api/competitions', name: 'api_competitions_create', methods: ['POST'])]
    public function create(
        Request $request,
        SportRepository $sportRepository,
        RegionRepository $regionRepository,
        CompetitionService $competitionService,
        CompetitionViewFactory $competitionViewFactory,
        ValidatorInterface $validator,
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['message' => 'Authentication required.'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        try {
            /** @var array{name?:mixed, sportId?:mixed, regionId?:mixed} $payload */
            $payload = $request->toArray();
        } catch (\JsonException) {
            return $this->json(['message' => 'Invalid JSON payload.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $name = is_string($payload['name'] ?? null) ? $payload['name'] : '';
        $sportId = $payload['sportId'] ?? null;
        $regionId = $payload['regionId'] ?? null;

        if (!is_int($sportId) && !(is_string($sportId) && ctype_digit($sportId))) {
            return $this->json(['message' => 'Valid sportId is required.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $sport = $sportRepository->findOneBy([
            'id' => (int) $sportId,
            'isActive' => true,
        ]);

        if (null === $sport) {
            return $this->json(['message' => 'Selected sport is invalid.'], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($competitionService->userHasCompetitionWithNameForSport($user, $sport, $name)) {
            return $this->json([
                'message' => 'Competition with this name already exists for this sport.',
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (null !== $regionId && !is_int($regionId) && !(is_string($regionId) && ctype_digit($regionId))) {
            return $this->json(['message' => 'Valid regionId is required.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $region = null;

        if (null !== $regionId) {
            $region = $regionRepository->findOneBy([
                'id' => (int) $regionId,
                'isActive' => true,
            ]);

            if (null === $region) {
                return $this->json(['message' => 'Selected region is invalid.'], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        $competition = $competitionService->createForUser($user, $sport, $name, $region);

        $violations = $validator->validate($competition);

        if (count($violations) > 0) {
            return $this->json([
                'message' => $violations[0]->getMessage(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $competitionService->save($competition);

        return $this->json(
            $competitionViewFactory->create($competition),
            JsonResponse::HTTP_CREATED
        );
    }
}
