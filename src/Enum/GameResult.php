<?php

namespace App\Enum;

enum GameResult
{
    case Win;
    case Draw;
    case Loss;

    public static function fromScore(int $for, int $against): self
    {
        return match ($for <=> $against) {
            1 => self::Win,
            0 => self::Draw,
            -1 => self::Loss,
        };
    }
}
