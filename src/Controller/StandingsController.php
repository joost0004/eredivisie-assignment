<?php

namespace App\Controller;

use App\Repository\GameRepository;
use App\Repository\TeamRepository;
use App\Standings\StandingsCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StandingsController extends AbstractController
{
    #[Route('/standings', name: 'app_standings')]
    public function index(TeamRepository $teams, GameRepository $games, StandingsCalculator $calculator): Response
    {
        return $this->render('standings/index.html.twig', [
            'rows' => $calculator->calculate($teams->findAll(), $games->findAll()),
        ]);
    }
}
