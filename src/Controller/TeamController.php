<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Team;
use App\Repository\GameRepository;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TeamController extends AbstractController
{
    #[Route('/teams', name: 'app_team_index')]
    public function index(TeamRepository $teams): Response
    {
        return $this->render('team/index.html.twig', [
            'teams' => $teams->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route('/teams/{id}', name: 'app_team_show')]
    public function show(Team $team, GameRepository $gameRepository): Response
    {
        $games = $gameRepository->findForTeam($team);
        $results = array_filter($games, fn (Game $game) => $game->getStatus()->isFinished());
        $fixtures = array_filter($games, fn (Game $game) => !$game->getStatus()->isFinished());
        $upcoming = array_filter($fixtures, fn (Game $game) => $game->getStatus()->isUpcoming());
        $next = reset($upcoming) ?: null;

        return $this->render('team/show.html.twig', [
            'team' => $team,
            'results' => $results,
            'fixtures' => $fixtures,
            'next_id' => $next?->getId(),
        ]);
    }
}
