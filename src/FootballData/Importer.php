<?php

namespace App\FootballData;

use App\Entity\Game;
use App\Entity\Team;
use App\Enum\GameStatus;
use App\Repository\GameRepository;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;

class Importer
{
    public function __construct(
        private readonly FootballDataClient $client,
        private readonly TeamRepository $teamRepository,
        private readonly GameRepository $gameRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function import(): ImportResult
    {
        $teamsData = $this->client->getTeams();
        $matchesData = $this->client->getMatches();

        $teams = $this->teamRepository->createQueryBuilder('t', 't.externalId')->getQuery()->getResult();
        $teamsCreated = 0;

        foreach ($teamsData as $data) {
            $team = $teams[$data['id']] ?? null;
            if (!$team) {
                $team = $teams[$data['id']] = (new Team())->setExternalId($data['id']);
                $this->entityManager->persist($team);
                $teamsCreated++;
            }

            $team
                ->setName($data['name'])
                ->setShortName($data['shortName'])
                ->setTla($data['tla'])
                ->setCrestUrl($data['crest'])
                ->setVenue($data['venue'])
                ->setFounded($data['founded'])
                ->setWebsiteUrl($data['website'])
                ->setClubColors($data['clubColors']);
        }

        $games = $this->gameRepository->createQueryBuilder('g', 'g.externalId')->getQuery()->getResult();
        $gamesCreated = 0;

        foreach ($matchesData as $data) {
            $game = $games[$data['id']] ?? null;
            if (!$game) {
                $game = $games[$data['id']] = (new Game())->setExternalId($data['id']);
                $this->entityManager->persist($game);
                $gamesCreated++;
            }

            $game
                ->setKickoffUtc(new \DateTimeImmutable($data['utcDate']))
                ->setMatchday($data['matchday'])
                ->setStatus(GameStatus::from($data['status']))
                ->setHomeTeam($teams[$data['homeTeam']['id']])
                ->setAwayTeam($teams[$data['awayTeam']['id']])
                ->setHomeScore($data['score']['fullTime']['home'])
                ->setAwayScore($data['score']['fullTime']['away']);
        }

        $unitOfWork = $this->entityManager->getUnitOfWork();
        $unitOfWork->computeChangeSets();

        $result = new ImportResult(
            teamsCreated: $teamsCreated,
            teamsUpdated: count(array_filter($teams, $unitOfWork->isScheduledForUpdate(...))),
            gamesCreated: $gamesCreated,
            gamesUpdated: count(array_filter($games, $unitOfWork->isScheduledForUpdate(...))),
        );

        $this->entityManager->flush();

        return $result;
    }
}
