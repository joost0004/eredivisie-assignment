<?php

namespace App\Enum;

enum GameStatus: string
{
    case Scheduled = 'SCHEDULED';
    case Timed = 'TIMED';
    case InPlay = 'IN_PLAY';
    case Paused = 'PAUSED';
    case ExtraTime = 'EXTRA_TIME';
    case PenaltyShootout = 'PENALTY_SHOOTOUT';
    case Finished = 'FINISHED';
    case Suspended = 'SUSPENDED';
    case Postponed = 'POSTPONED';
    case Cancelled = 'CANCELLED';
    case Awarded = 'AWARDED';
}
