<?php

namespace App\Controller;

use App\Api\BetSlipViewFactory;
use App\Entity\User;
use App\Service\BetSlipService;
use App\Service\Exception\BetSlipNotFoundException;
use App\Service\Exception\BetSlipValidationException;
use App\Service\Input\BetSlipSettlementInput;
use App\Service\Input\BetSlipUpsertInput;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/api/bet-slips')]
final class BetSlipController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    #[Route('', name: 'api_bet_slips_index', methods: ['GET'])]
    public function index(Request $request, BetSlipService $betSlipService, BetSlipViewFactory $betSlipViewFactory): JsonResponse
    {
        $user = $this->requireUser();
        if ($user instanceof JsonResponse) {
            return $user;
        }

        $page = max(1, (int) $request->query->get('page', '1'));
        $perPage = max(1, (int) $request->query->get('perPage', '20'));
        $result = $betSlipService->paginateForUser($user, $request->query->all(), $page, $perPage);

        return $this->json([
            'items' => array_map($betSlipViewFactory->create(...), $result['items']),
            'pagination' => [
                'page' => $result['page'],
                'perPage' => $result['perPage'],
                'total' => $result['total'],
            ],
        ]);
    }

    #[Route('/{id}', name: 'api_bet_slips_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id, BetSlipService $betSlipService, BetSlipViewFactory $betSlipViewFactory): JsonResponse
    {
        $user = $this->requireUser();
        if ($user instanceof JsonResponse) {
            return $user;
        }

        try {
            return $this->json($betSlipViewFactory->create($betSlipService->getForUser($user, $id)));
        } catch (BetSlipNotFoundException) {
            return $this->json(['message' => $this->translator->trans('bet_slip.api.not_found')], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    #[Route('', name: 'api_bet_slips_create', methods: ['POST'])]
    public function create(Request $request, BetSlipService $betSlipService, BetSlipViewFactory $betSlipViewFactory): JsonResponse
    {
        $user = $this->requireUser();
        if ($user instanceof JsonResponse) {
            return $user;
        }

        try {
            $betSlip = $betSlipService->createForUser($user, $this->mapUpsertInput($request));
        } catch (BetSlipValidationException $exception) {
            return $this->unprocessable($exception);
        } catch (\JsonException) {
            return $this->json(['message' => $this->translator->trans('api.invalid_json')], JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->json($betSlipViewFactory->create($betSlip), JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_bet_slips_update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(int $id, Request $request, BetSlipService $betSlipService, BetSlipViewFactory $betSlipViewFactory): JsonResponse
    {
        $user = $this->requireUser();
        if ($user instanceof JsonResponse) {
            return $user;
        }

        try {
            $betSlip = $betSlipService->updateForUser($user, $id, $this->mapUpsertInput($request));
        } catch (BetSlipNotFoundException) {
            return $this->json(['message' => $this->translator->trans('bet_slip.api.not_found')], JsonResponse::HTTP_NOT_FOUND);
        } catch (BetSlipValidationException $exception) {
            return $this->unprocessable($exception);
        } catch (\JsonException) {
            return $this->json(['message' => $this->translator->trans('api.invalid_json')], JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->json($betSlipViewFactory->create($betSlip));
    }

    #[Route('/{id}', name: 'api_bet_slips_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(int $id, BetSlipService $betSlipService): JsonResponse
    {
        $user = $this->requireUser();
        if ($user instanceof JsonResponse) {
            return $user;
        }

        try {
            $betSlipService->deleteForUser($user, $id);
        } catch (BetSlipNotFoundException) {
            return $this->json(['message' => $this->translator->trans('bet_slip.api.not_found')], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/{id}/settle', name: 'api_bet_slips_settle', methods: ['PATCH'], requirements: ['id' => '\d+'])]
    public function settle(int $id, Request $request, BetSlipService $betSlipService, BetSlipViewFactory $betSlipViewFactory): JsonResponse
    {
        $user = $this->requireUser();
        if ($user instanceof JsonResponse) {
            return $user;
        }

        try {
            $betSlip = $betSlipService->settleForUser($user, $id, $this->mapSettlementInput($request));
        } catch (BetSlipNotFoundException) {
            return $this->json(['message' => $this->translator->trans('bet_slip.api.not_found')], JsonResponse::HTTP_NOT_FOUND);
        } catch (BetSlipValidationException $exception) {
            return $this->unprocessable($exception);
        } catch (\JsonException) {
            return $this->json(['message' => $this->translator->trans('api.invalid_json')], JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->json($betSlipViewFactory->create($betSlip));
    }

    private function mapUpsertInput(Request $request): BetSlipUpsertInput
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();

        $bookmakerId = $payload['bookmakerId'] ?? null;
        if (!is_int($bookmakerId) && !(is_string($bookmakerId) && ctype_digit($bookmakerId))) {
            throw new BetSlipValidationException('bet_slip.api.bookmaker_required');
        }

        return new BetSlipUpsertInput(
            is_string($payload['name'] ?? null) ? $payload['name'] : null,
            is_string($payload['description'] ?? null) ? $payload['description'] : '',
            (int) $bookmakerId,
            $this->normalizeDecimal($payload['stake'] ?? null, 2, 'bet_slip.api.stake_required'),
            $this->normalizeDecimal($payload['totalOdds'] ?? null, 3, 'bet_slip.api.total_odds_required'),
            is_string($payload['stakeType'] ?? null) ? trim($payload['stakeType']) : 'cash',
            is_string($payload['timing'] ?? null) ? trim($payload['timing']) : 'pre_match',
            is_int($payload['selectionsCount'] ?? null) ? $payload['selectionsCount'] : (ctype_digit((string) ($payload['selectionsCount'] ?? '')) ? (int) $payload['selectionsCount'] : null),
            $this->normalizeDecimal($payload['taxPercent'] ?? null, 2, 'bet_slip.api.tax_percent_required'),
            $this->normalizeIdList($payload['competitionIds'] ?? []),
            $this->normalizeIdList($payload['tagIds'] ?? []),
            $this->normalizeDateTime($payload['placedAt'] ?? null, 'bet_slip.api.placed_at_required'),
        );
    }

    private function mapSettlementInput(Request $request): BetSlipSettlementInput
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();
        $status = is_string($payload['status'] ?? null) ? trim($payload['status']) : '';

        if ('' === $status) {
            throw new BetSlipValidationException('bet_slip.api.status_required');
        }

        return new BetSlipSettlementInput(
            $status,
            null === ($payload['actualPayout'] ?? null) || '' === trim((string) $payload['actualPayout'])
                ? null
                : $this->normalizeDecimal($payload['actualPayout'], 2, 'bet_slip.api.actual_payout_invalid'),
            null === ($payload['settledAt'] ?? null) || '' === trim((string) $payload['settledAt'])
                ? null
                : $this->normalizeDateTime($payload['settledAt'], 'bet_slip.api.settled_at_invalid'),
        );
    }

    /**
     * @return list<int>
     */
    private function normalizeIdList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $ids = [];

        foreach ($value as $item) {
            if (is_int($item) || (is_string($item) && ctype_digit($item))) {
                $ids[] = (int) $item;
            }
        }

        return array_values(array_unique($ids));
    }

    private function normalizeDecimal(mixed $value, int $scale, string $message): string
    {
        if (is_int($value) || is_float($value) || is_string($value)) {
            $normalized = trim((string) $value);

            if ('' !== $normalized && preg_match('/^\d+(?:\.\d+)?$/', $normalized)) {
                return number_format((float) $normalized, $scale, '.', '');
            }
        }

        throw new BetSlipValidationException($message);
    }

    private function normalizeDateTime(mixed $value, string $message): \DateTimeImmutable
    {
        if (is_string($value)) {
            try {
                $dateTime = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, trim($value))
                    ?: new \DateTimeImmutable(trim($value));
            } catch (\Exception) {
                throw new BetSlipValidationException($message);
            }

            if ($dateTime instanceof \DateTimeImmutable) {
                return $dateTime;
            }
        }

        throw new BetSlipValidationException($message);
    }

    private function unprocessable(BetSlipValidationException $exception): JsonResponse
    {
        return $this->json(['message' => $this->translator->trans($exception->getMessage())], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function requireUser(): User|JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['message' => $this->translator->trans('api.auth_required')], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return $user;
    }
}
