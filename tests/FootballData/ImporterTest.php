<?php

namespace App\Tests\FootballData;

use App\Entity\Game;
use App\Entity\Team;
use App\Enum\GameStatus;
use App\FootballData\FootballDataClient;
use App\FootballData\Importer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;

class ImporterTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->entityManager->createQuery('DELETE FROM '.Game::class)->execute();
        $this->entityManager->createQuery('DELETE FROM '.Team::class)->execute();
    }

    // maps API data to entities. kickoff must come back as the same UTC moment
    public function testFirstImportCreatesTeamsAndGames(): void
    {
        $result = $this->importer([$this->teamsResponse(), $this->matchesResponse(2, 1)])->import();

        $this->assertSame([2, 0, 1, 0], [$result->teamsCreated, $result->teamsUpdated, $result->gamesCreated, $result->gamesUpdated]);

        // read from the database, not doctrine's memory: catches the 2-hour timezone shift
        $this->entityManager->clear();
        $game = $this->entityManager->getRepository(Game::class)->findOneBy(['externalId' => 100]);

        $this->assertSame('PSV', $game->getHomeTeam()->getName());
        $this->assertSame('Ajax', $game->getAwayTeam()->getName());
        $this->assertSame(GameStatus::Finished, $game->getStatus());
        $this->assertSame([2, 1], [$game->getHomeScore(), $game->getAwayScore()]);
        $this->assertEquals(new \DateTimeImmutable('2026-08-07T18:00:00Z'), $game->getKickoffUtc());
    }

    // reimporting creates no duplicates and only counts rows that really changed
    public function testReimportUpdatesOnlyChangedRows(): void
    {
        $importer = $this->importer([
            $this->teamsResponse(), $this->matchesResponse(2, 1),
            $this->teamsResponse(), $this->matchesResponse(2, 1),
            $this->teamsResponse(), $this->matchesResponse(3, 1),
        ]);
        $importer->import();
        $this->entityManager->clear();

        // catches every game counting as updated because of a new date instance
        $unchanged = $importer->import();
        $this->entityManager->clear();

        $changed = $importer->import();

        $this->assertSame([0, 0, 0, 0], [$unchanged->teamsCreated, $unchanged->teamsUpdated, $unchanged->gamesCreated, $unchanged->gamesUpdated]);
        $this->assertSame([0, 0, 0, 1], [$changed->teamsCreated, $changed->teamsUpdated, $changed->gamesCreated, $changed->gamesUpdated]);
        $this->assertSame(2, $this->entityManager->getRepository(Team::class)->count([]));
        $this->assertSame(1, $this->entityManager->getRepository(Game::class)->count([]));
    }

    private function importer(array $responses): Importer
    {
        self::getContainer()->set(FootballDataClient::class, new FootballDataClient(new MockHttpClient($responses)));

        return self::getContainer()->get(Importer::class);
    }

    private function teamsResponse(): JsonMockResponse
    {
        return new JsonMockResponse(['teams' => [
            $this->team(1, 'PSV'),
            $this->team(2, 'Ajax'),
        ]]);
    }

    private function team(int $id, string $name): array
    {
        return [
            'id' => $id,
            'name' => $name,
            'shortName' => $name,
            'tla' => strtoupper(substr($name, 0, 3)),
            'crest' => "https://crests.football-data.org/$id.png",
            'venue' => "$name Stadium",
            'founded' => 1900,
            'website' => "https://$name.example",
            'clubColors' => 'Red / White',
        ];
    }

    private function matchesResponse(int $homeScore, int $awayScore): JsonMockResponse
    {
        return new JsonMockResponse(['matches' => [[
            'id' => 100,
            'season' => ['id' => 2493],
            'utcDate' => '2026-08-07T18:00:00Z',
            'matchday' => 1,
            'status' => 'FINISHED',
            'homeTeam' => ['id' => 1],
            'awayTeam' => ['id' => 2],
            'score' => ['fullTime' => ['home' => $homeScore, 'away' => $awayScore]],
        ]]]);
    }
}