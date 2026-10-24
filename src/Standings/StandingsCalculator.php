<?php

namespace App\Standings;

class StandingsCalculator
{
    public function calculate(array $teams, array $games): array
    {
        $rows = [];
        foreach ($teams as $team) {
            $rows[spl_object_id($team)] = new StandingRow($team);
        }

        foreach ($games as $game) {
            if (!$game->getStatus()->isFinished()) {
                continue;
            }
            $rows[spl_object_id($game->getHomeTeam())]->addResult($game->getHomeScore(), $game->getAwayScore());
            $rows[spl_object_id($game->getAwayTeam())]->addResult($game->getAwayScore(), $game->getHomeScore());
        }

        // highest first; each next line only decides when the ones above are equal
        usort($rows, fn (StandingRow $a, StandingRow $b) => $b->getPoints() <=> $a->getPoints()
            ?: $b->getGoalDifference() <=> $a->getGoalDifference()
            ?: $b->goalsFor <=> $a->goalsFor
            ?: $a->team->getName() <=> $b->team->getName());

        return $rows;
    }
}
