<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Team>
 */
class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    // every team has home games in a season so a join is enough
    public function findBySeason(?int $season): array
    {
        return $this->createQueryBuilder('team')
            ->join(Game::class, 'game', 'WITH', 'game.homeTeam = team')
            ->where('game.seasonExternalId = :season')
            ->setParameter('season', $season)
            ->groupBy('team')
            ->orderBy('team.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
