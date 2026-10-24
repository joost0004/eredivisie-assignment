<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Team;
use App\Enum\GameStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    public function findUpcoming(int $page, int $perPage): Paginator
    {
        $query = $this->createQueryBuilder('game')
            ->addSelect('homeTeam', 'awayTeam')
            ->join('game.homeTeam', 'homeTeam')
            ->join('game.awayTeam', 'awayTeam')
            ->where('game.status IN (:statuses)')
            ->setParameter('statuses', [GameStatus::Scheduled, GameStatus::Timed])
            ->orderBy('game.kickoffUtc', 'ASC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery();

        return new Paginator($query, fetchJoinCollection: false);
    }

    public function findLatestFinishedForTeam(Team $team): ?Game
    {
        return $this->findOneForTeam($team, [GameStatus::Finished, GameStatus::Awarded], 'DESC');
    }

    public function findNextForTeam(Team $team): ?Game
    {
        return $this->findOneForTeam($team, [GameStatus::Scheduled, GameStatus::Timed], 'ASC');
    }

    private function findOneForTeam(Team $team, array $statuses, string $order): ?Game
    {
        return $this->createQueryBuilder('game')
            ->where('game.homeTeam = :team OR game.awayTeam = :team')
            ->andWhere('game.status IN (:statuses)')
            ->setParameter('team', $team)
            ->setParameter('statuses', $statuses)
            ->orderBy('game.kickoffUtc', $order)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
