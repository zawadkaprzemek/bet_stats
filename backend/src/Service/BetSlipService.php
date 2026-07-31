<?php

namespace App\Service;

use App\Entity\BetSlip;
use App\Entity\Bookmaker;
use App\Entity\Competition;
use App\Entity\Tag;
use App\Entity\User;
use App\Enum\BetSlipStakeType;
use App\Enum\BetSlipTiming;
use App\Repository\BetSlipRepository;
use App\Repository\BookmakerRepository;
use App\Repository\CompetitionRepository;
use App\Repository\TagRepository;
use App\Service\Exception\BetSlipNotFoundException;
use App\Service\Exception\BetSlipValidationException;
use App\Service\Input\BetSlipSettlementInput;
use App\Service\Input\BetSlipUpsertInput;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class BetSlipService
{
    public function __construct(
        private BetSlipRepository $betSlipRepository,
        private BookmakerRepository $bookmakerRepository,
        private CompetitionRepository $competitionRepository,
        private TagRepository $tagRepository,
        private BetSlipSettlementService $settlementService,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
    ) {
    }

    /**
     * @return array{items:list<BetSlip>, total:int, page:int, perPage:int}
     */
    public function paginateForUser(User $owner, array $filters, int $page, int $perPage): array
    {
        $page = max(1, $page);
        $perPage = min(100, max(1, $perPage));
        $queryBuilder = $this->betSlipRepository->createFilteredOwnedQueryBuilder($owner, $filters)
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);
        $paginator = new Paginator($queryBuilder->getQuery(), true);

        return [
            'items' => array_values(iterator_to_array($paginator->getIterator())),
            'total' => count($paginator),
            'page' => $page,
            'perPage' => $perPage,
        ];
    }

    public function getForUser(User $owner, int $betSlipId): BetSlip
    {
        $betSlip = $this->betSlipRepository->findOneOwnedByUser($betSlipId, $owner);

        if (!$betSlip instanceof BetSlip) {
            throw new BetSlipNotFoundException('bet_slip.api.not_found');
        }

        return $betSlip;
    }

    public function createForUser(User $owner, BetSlipUpsertInput $input): BetSlip
    {
        $betSlip = new BetSlip();
        $betSlip->setOwner($owner);

        $this->hydrate($betSlip, $owner, $input);
        $this->save($betSlip);

        return $betSlip;
    }

    public function updateForUser(User $owner, int $betSlipId, BetSlipUpsertInput $input): BetSlip
    {
        $betSlip = $this->getForUser($owner, $betSlipId);
        $this->hydrate($betSlip, $owner, $input);
        $this->save($betSlip);

        return $betSlip;
    }

    public function settleForUser(User $owner, int $betSlipId, BetSlipSettlementInput $input): BetSlip
    {
        $betSlip = $this->getForUser($owner, $betSlipId);
        $this->settlementService->settle($betSlip, $input);
        $this->validateBusinessRules($betSlip);
        $this->save($betSlip);

        return $betSlip;
    }

    public function deleteForUser(User $owner, int $betSlipId): void
    {
        $betSlip = $this->getForUser($owner, $betSlipId);
        $this->entityManager->remove($betSlip);
        $this->entityManager->flush();
    }

    private function hydrate(BetSlip $betSlip, User $owner, BetSlipUpsertInput $input): void
    {
        $bookmaker = $this->resolveBookmaker($input->bookmakerId, $betSlip->getBookmaker());
        $competitions = $this->resolveCompetitions($owner, $input->competitionIds, $betSlip);
        $tags = $this->resolveTags($owner, $input->tagIds);

        $betSlip
            ->setBookmaker($bookmaker)
            ->setName($input->name)
            ->setDescription($input->description)
            ->setStake($input->stake)
            ->setTotalOdds($input->totalOdds)
            ->setSelectionsCount($input->selectionsCount)
            ->setTaxPercent($input->taxPercent)
            ->setPlacedAt($input->placedAt);

        try {
            $betSlip
                ->setStakeType(BetSlipStakeType::from($input->stakeType))
                ->setTiming(BetSlipTiming::from($input->timing));
        } catch (\ValueError) {
            throw new BetSlipValidationException('bet_slip.api.status_invalid');
        }

        $betSlip->clearCompetitions();
        foreach ($competitions as $competition) {
            $betSlip->addCompetition($competition);
        }

        $betSlip->clearTags();
        foreach ($tags as $tag) {
            $betSlip->addTag($tag);
        }

        $this->validateBusinessRules($betSlip);
    }

    private function save(BetSlip $betSlip): void
    {
        $this->entityManager->persist($betSlip);
        $this->entityManager->flush();
    }

    private function resolveBookmaker(int $bookmakerId, ?Bookmaker $currentBookmaker): Bookmaker
    {
        $bookmaker = $this->bookmakerRepository->find($bookmakerId);

        if (!$bookmaker instanceof Bookmaker) {
            throw new BetSlipValidationException('bet_slip.api.bookmaker_invalid');
        }

        if (!$bookmaker->isActive() && $bookmaker->getId() !== $currentBookmaker?->getId()) {
            throw new BetSlipValidationException('bet_slip.api.bookmaker_inactive');
        }

        return $bookmaker;
    }

    /**
     * @return list<Competition>
     */
    private function resolveCompetitions(User $owner, array $competitionIds, BetSlip $betSlip): array
    {
        $competitionIds = array_values(array_unique(array_map('intval', $competitionIds)));

        if ([] === $competitionIds) {
            return [];
        }

        $currentIds = array_map(
            static fn (Competition $competition): int => $competition->getId() ?? 0,
            $betSlip->getCompetitions()->toArray(),
        );

        $competitions = $this->competitionRepository->findAssignableByIdsForUser($owner, $competitionIds, $currentIds);

        if (count($competitions) !== count($competitionIds)) {
            throw new BetSlipValidationException('bet_slip.api.competition_invalid');
        }

        return $competitions;
    }

    /**
     * @return list<Tag>
     */
    private function resolveTags(User $owner, array $tagIds): array
    {
        $tagIds = array_values(array_unique(array_map('intval', $tagIds)));

        if ([] === $tagIds) {
            return [];
        }

        $tags = $this->tagRepository->findVisibleByIdsForUser($owner, $tagIds);

        if (count($tags) !== count($tagIds)) {
            throw new BetSlipValidationException('bet_slip.api.tag_invalid');
        }

        return $tags;
    }

    private function validateBusinessRules(BetSlip $betSlip): void
    {
        $violations = $this->validator->validate($betSlip);

        if (count($violations) > 0) {
            throw new BetSlipValidationException((string) $violations[0]->getMessage());
        }

        if ('' === trim($betSlip->getDescription())) {
            throw new BetSlipValidationException('bet_slip.validation.description_required');
        }

        if ((float) $betSlip->getStake() <= 0) {
            throw new BetSlipValidationException('bet_slip.validation.stake_positive');
        }

        if ((float) $betSlip->getTotalOdds() < 1.001) {
            throw new BetSlipValidationException('bet_slip.validation.total_odds_min');
        }

        $taxPercent = (float) $betSlip->getTaxPercent();
        if ($taxPercent < 0 || $taxPercent > 100) {
            throw new BetSlipValidationException('bet_slip.validation.tax_percent_range');
        }

        if (null !== $betSlip->getSelectionsCount() && $betSlip->getSelectionsCount() <= 0) {
            throw new BetSlipValidationException('bet_slip.validation.selections_count_positive');
        }

        $status = $betSlip->getStatus();
        $actualPayout = $betSlip->getActualPayout();

        if ($status->isSettled() && null === $betSlip->getSettledAt()) {
            throw new BetSlipValidationException('bet_slip.validation.settled_at_required');
        }

        if (!$status->isSettled() && null !== $betSlip->getSettledAt()) {
            throw new BetSlipValidationException('bet_slip.validation.pending_settlement_fields');
        }

        if (!$status->isSettled() && null !== $actualPayout) {
            throw new BetSlipValidationException('bet_slip.validation.pending_settlement_fields');
        }

        if ($status->isSettled() && null === $actualPayout) {
            throw new BetSlipValidationException('bet_slip.validation.actual_payout_required');
        }
    }
}
