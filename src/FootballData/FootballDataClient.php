<?php

namespace App\FootballData;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class FootballDataClient
{
    public function __construct(
        private readonly HttpClientInterface $footballDataClient,
    ) {
    }

    public function getTeams(): array
    {
        return $this->footballDataClient->request('GET', 'competitions/DED/teams')->toArray()['teams'];
    }

    public function getMatches(): array
    {
        return $this->footballDataClient->request('GET', 'competitions/DED/matches')->toArray()['matches'];
    }
}
