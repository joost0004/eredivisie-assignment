<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function findForTeam(Team $team): array
    {
        return $this->createQueryBuilder('game')
            ->addSelect('homeTeam', 'awayTeam')
            ->join('game.homeTeam', 'homeTeam')
            ->join('game.awayTeam', 'awayTeam')
            ->where('homeTeam = :team OR awayTeam = :team')
            ->setParameter('team', $team)
            ->orderBy('game.kickoffUtc', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
