<?php

namespace App\Standings;

use App\Entity\Team;
use App\Enum\GameResult;

class StandingRow
{
    public int $played = 0;
    public int $won = 0;
    public int $drawn = 0;
    public int $lost = 0;
    public int $goalsFor = 0;
    public int $goalsAgainst = 0;

    public function __construct(public readonly Team $team)
    {
    }

    public function addResult(int $for, int $against): void
    {
        ++$this->played;
        $this->goalsFor += $for;
        $this->goalsAgainst += $against;

        match (GameResult::fromScore($for, $against)) {
            GameResult::Win => ++$this->won,
            GameResult::Draw => ++$this->drawn,
            GameResult::Loss => ++$this->lost,
        };
    }

    public function getGoalDifference(): int
    {
        return $this->goalsFor - $this->goalsAgainst;
    }

    public function getPoints(): int
    {
        return $this->won * 3 + $this->drawn;
    }
}
