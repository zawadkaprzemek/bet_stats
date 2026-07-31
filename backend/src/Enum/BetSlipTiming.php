<?php

namespace App\Enum;

enum BetSlipTiming: string
{
    case PRE_MATCH = 'pre_match';
    case LIVE = 'live';
    case HYBRID = 'hybrid';
}
