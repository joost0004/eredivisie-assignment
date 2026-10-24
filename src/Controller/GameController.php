<?php

namespace App\Controller;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class GameController extends AbstractController
{
    private const PER_PAGE = 20;

    #[Route('/matches', name: 'app_game_index')]
    public function index(GameRepository $gameRepository, #[MapQueryParameter(options: ['min_range' => 1])] int $page = 1): Response
    {
        $games = $gameRepository->findUpcoming($page, self::PER_PAGE);
        $total = count($games);
        $pages = (int) ceil($total / self::PER_PAGE);

        if ($page > 1 && $page > $pages) {
            throw $this->createNotFoundException();
        }

        return $this->render('game/index.html.twig', [
            'games' => $games,
            'total' => $total,
            'page' => $page,
            'pages' => $pages,
        ]);
    }
}
