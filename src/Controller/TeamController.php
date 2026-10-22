<?php

namespace App\Controller;

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
}
