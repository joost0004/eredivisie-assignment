<?php

namespace App\Tests\Standings;

use App\Entity\Game;
use App\Entity\Team;
use App\Enum\GameStatus;
use App\Standings\StandingRow;
use App\Standings\StandingsCalculator;
use PHPUnit\Framework\TestCase;

class StandingsCalculatorTest extends TestCase
{
    public function testCountsOnlyFinishedGames(): void
    {
        $ajax = $this->team('Ajax');
        $psv = $this->team('PSV');

        $rows = (new StandingsCalculator())->calculate([$ajax, $psv], [
            $this->game($ajax, $psv, GameStatus::Finished, 3, 1),
            $this->game($psv, $ajax, GameStatus::Timed, null, null),
        ]);

        [$first, $second] = $rows;
        $this->assertSame($ajax, $first->team);
        $this->assertSame([1, 1, 0, 0, 3, 1, 2, 3], $this->stats($first));
        $this->assertSame([1, 0, 0, 1, 1, 3, -2, 0], $this->stats($second));
    }

    // equal points: goal difference, then goals scored, then name decide
    public function testTiesAreBrokenInOrder(): void
    {
        $az = $this->team('AZ');
        $nec = $this->team('NEC');
        $psv = $this->team('PSV');
        $twente = $this->team('Twente');
        $other = $this->team('Other');

        $rows = (new StandingsCalculator())->calculate([$twente, $psv, $nec, $az, $other], [
            $this->game($az, $other, GameStatus::Finished, 3, 0),
            $this->game($nec, $other, GameStatus::Finished, 4, 2),
            $this->game($psv, $other, GameStatus::Finished, 2, 0),
            $this->game($twente, $other, GameStatus::Finished, 2, 0),
        ]);

        $names = array_map(fn (StandingRow $row) => $row->team->getName(), $rows);
        $this->assertSame(['AZ', 'NEC', 'PSV', 'Twente', 'Other'], $names);
    }

    private function stats(StandingRow $row): array
    {
        return [$row->played, $row->won, $row->drawn, $row->lost, $row->goalsFor, $row->goalsAgainst, $row->getGoalDifference(), $row->getPoints()];
    }

    private function team(string $name): Team
    {
        return (new Team())->setName($name);
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
