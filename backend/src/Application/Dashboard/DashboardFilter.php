<?php

namespace App\Application\Dashboard;

use App\Enum\BetSlipStatus;

final readonly class DashboardFilter
{
    public function __construct(
        public ?\DateTimeImmutable $dateFrom = null,
        public ?\DateTimeImmutable $dateTo = null,
        public ?int $bookmakerId = null,
        public ?int $sportId = null,
        public ?int $competitionId = null,
        public ?int $tagId = null,
        public ?BetSlipStatus $status = null,
    ) {
    }

    /**
     * @param array<string, mixed> $filters
     */
    public static function fromArray(array $filters): self
    {
        return new self(
            self::normalizeDateTime($filters['dateFrom'] ?? null, false),
            self::normalizeDateTime($filters['dateTo'] ?? null, true),
            self::normalizeInt($filters['bookmakerId'] ?? null),
            self::normalizeInt($filters['sportId'] ?? null),
            self::normalizeInt($filters['competitionId'] ?? null),
            self::normalizeInt($filters['tagId'] ?? null),
            self::normalizeStatus($filters['status'] ?? null),
        );
    }

    private static function normalizeInt(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value > 0 ? $value : null;
        }

        if (is_string($value) && ctype_digit(trim($value))) {
            $normalized = (int) trim($value);

            return $normalized > 0 ? $normalized : null;
        }

        return null;
    }

    private static function normalizeDateTime(mixed $value, bool $isDateTo): ?\DateTimeImmutable
    {
        if (!is_string($value) || '' === trim($value)) {
            return null;
        }

        $normalized = trim($value);
        $dateTime = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $normalized)
            ?: \DateTimeImmutable::createFromFormat('!Y-m-d', $normalized);

        if (!$dateTime instanceof \DateTimeImmutable) {
            return null;
        }

        if ($isDateTo && 10 === strlen($normalized)) {
            return $dateTime->setTime(23, 59, 59);
        }

        return $dateTime;
    }

    private static function normalizeStatus(mixed $value): ?BetSlipStatus
    {
        if (!is_string($value) || '' === trim($value)) {
            return null;
        }

        return BetSlipStatus::tryFrom(trim($value));
    }
}
