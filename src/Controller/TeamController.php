<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\User;
use App\Repository\GameRepository;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TeamController extends AbstractController
{
    #[Route('/teams', name: 'app_team_index')]
    public function index(TeamRepository $teams, GameRepository $games): Response
    {
        return $this->render('team/index.html.twig', [
            'teams' => $teams->findBySeason($games->findCurrentSeasonId()),
        ]);
    }

    #[Route('/teams/{id}', name: 'app_team_show')]
    public function show(Team $team, GameRepository $gameRepository): Response
    {
        $games = $gameRepository->findForTeam($team, $gameRepository->findCurrentSeasonId());
        $results = array_reverse(array_filter($games, fn (Game $game) => $game->getStatus()->isFinished()));
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

    // visitors get a GET link here, so after logging in Symfony sends them back to it
    #[Route('/teams/{id}/follow', name: 'app_team_follow', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function follow(Team $team, Request $request, #[CurrentUser] User $user, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST') && $this->isCsrfTokenValid('follow'.$team->getId(), $request->getPayload()->getString('_token'))) {
            $user->isFollowing($team) ? $user->unfollow($team) : $user->follow($team);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_team_show', ['id' => $team->getId()]);
    }
}
