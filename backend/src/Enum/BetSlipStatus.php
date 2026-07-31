<?php

namespace App\Enum;

enum BetSlipStatus: string
{
    case PENDING = 'pending';
    case WON = 'won';
    case LOST = 'lost';
    case VOID = 'void';
    case CASHOUT = 'cashout';
    case PARTIAL_CASHOUT = 'partial_cashout';

    public function isSettled(): bool
    {
        return self::PENDING !== $this;
    }
}
