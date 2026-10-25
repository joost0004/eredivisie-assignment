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

    public static function finished(): array
    {
        return [self::Finished, self::Awarded];
    }

    public static function upcoming(): array
    {
        return [self::Scheduled, self::Timed];
    }

    public function isFinished(): bool
    {
        return in_array($this, self::finished(), true);
    }

    public function isLive(): bool
    {
        return in_array($this, [self::InPlay, self::Paused, self::ExtraTime, self::PenaltyShootout], true);
    }

    public function isUpcoming(): bool
    {
        return in_array($this, self::upcoming(), true);
    }

    public function isTimed(): bool
    {
        return $this === self::Timed;
    }
}
