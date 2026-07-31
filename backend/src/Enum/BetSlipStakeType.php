<?php

namespace App\Enum;

enum BetSlipStakeType: string
{
    case CASH = 'cash';
    case FREEBET = 'freebet';
    case BONUS = 'bonus';
}
