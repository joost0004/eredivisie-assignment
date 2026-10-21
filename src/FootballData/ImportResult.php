<?php

namespace App\FootballData;

readonly class ImportResult
{
    public function __construct(
        public int $teamsCreated,
        public int $teamsUpdated,
        public int $gamesCreated,
        public int $gamesUpdated,
    ) {
    }
}
