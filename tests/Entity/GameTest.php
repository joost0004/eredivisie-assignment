<?php

namespace App\Tests\Entity;

use App\Entity\Game;
use App\Entity\Team;
use App\Enum\GameStatus;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    public function testResultIsSeenFromEachTeamsSide(): void
    {
        $home = new Team();
        $away = new Team();
        $game = $this->game($home, $away, GameStatus::Finished, 2, 1);

        $this->assertSame('Win', $game->resultFor($home));
        $this->assertSame('Loss', $game->resultFor($away));
    }

    private function game(Team $home, Team $away, GameStatus $status, ?int $homeScore, ?int $awayScore): Game
    {
        return (new Game())
            ->setHomeTeam($home)
            ->setAwayTeam($away)
            ->setStatus($status)
            ->setHomeScore($homeScore)
            ->setAwayScore($awayScore);
    }
}
