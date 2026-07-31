<?php

namespace App\Controller;

use App\Application\Dashboard\DashboardFilter;
use App\Application\Dashboard\DashboardSummaryService;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/api/dashboard/summary')]
final class DashboardSummaryController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    #[Route('', name: 'api_dashboard_summary', methods: ['GET'])]
    public function __invoke(Request $request, DashboardSummaryService $dashboardSummaryService): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['message' => $this->translator->trans('api.auth_required')], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $summary = $dashboardSummaryService->summarizeForUser($user, DashboardFilter::fromArray($request->query->all()));

        return $this->json([
            'summary' => $summary->toArray(),
        ]);
    }
}
