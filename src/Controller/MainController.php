<?php

namespace App\Controller;

use App\Service\NbaApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MainController extends AbstractController
{
    public function __construct(
        private NbaApiService $nbaApiService
    ) {}

    #[Route('/', name: 'app_home')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_players');
    }

    #[Route('/players', name: 'app_players')]
    #[IsGranted('ROLE_USER')]
    public function players(Request $request): Response
    {
        try {
            $page = $request->query->getInt('page', 1);
            $playersData = $this->nbaApiService->getPlayers($page, 25);
            
            return $this->render('main/players.html.twig', [
                'players' => $playersData['data'] ?? [],
                'meta' => $playersData['meta'] ?? [],
                'currentPage' => $page,
                'error' => $playersData['error'] ?? null,
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la récupération des joueurs');
            return $this->render('main/players.html.twig', [
                'players' => [],
                'meta' => [],
                'currentPage' => 1,
                'error' => $e->getMessage(),
            ]);
        }
    }

    #[Route('/teams', name: 'app_teams')]
    #[IsGranted('ROLE_USER')]
    public function teams(Request $request): Response
    {
        try {
            $page = $request->query->getInt('page', 1);
            $teamsData = $this->nbaApiService->getTeams($page, 30);
            
            return $this->render('main/teams.html.twig', [
                'teams' => $teamsData['data'] ?? [],
                'meta' => $teamsData['meta'] ?? [],
                'currentPage' => $page,
                'error' => $teamsData['error'] ?? null,
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la récupération des équipes');
            return $this->render('main/teams.html.twig', [
                'teams' => [],
                'meta' => [],
                'currentPage' => 1,
                'error' => $e->getMessage(),
            ]);
        }
    }

    #[Route('/games', name: 'app_games')]
    #[IsGranted('ROLE_USER')]
    public function games(Request $request): Response
    {
        try {
            $page = $request->query->getInt('page', 1);
            $gamesData = $this->nbaApiService->getGames($page, 25);
            
            return $this->render('main/games.html.twig', [
                'games' => $gamesData['data'] ?? [],
                'meta' => $gamesData['meta'] ?? [],
                'currentPage' => $page,
                'error' => $gamesData['error'] ?? null,
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la récupération des matchs');
            return $this->render('main/games.html.twig', [
                'games' => [],
                'meta' => [],
                'currentPage' => 1,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
