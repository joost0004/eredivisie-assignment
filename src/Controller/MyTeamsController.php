<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MyTeamsController extends AbstractController
{
    #[Route('/my-teams', name: 'app_my_teams')]
    #[IsGranted('ROLE_USER')]
    public function index(#[CurrentUser] User $user, GameRepository $games): Response
    {
        $rows = [];
        foreach ($user->getFollowedTeams() as $team) {
            $latest = $games->findLatestFinishedForTeam($team);
            $rows[] = [
                'team' => $team,
                'game' => $latest ?? $games->findNextForTeam($team),
                'played' => $latest !== null,
            ];
        }

        return $this->render('my_teams/index.html.twig', [
            'rows' => $rows,
        ]);
    }
}
