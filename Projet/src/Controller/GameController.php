<?php

namespace App\Controller;

use App\Service\NbaApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/game')]
#[IsGranted('ROLE_USER')]
class GameController extends AbstractController
{
    public function __construct(
        private NbaApiService $nbaApiService
    ) {}

    #[Route('/{id}', name: 'app_game_show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        try {
            $game = $this->nbaApiService->getGame($id);
            
            if (isset($game['error'])) {
                $this->addFlash('error', 'Erreur: ' . $game['error']);
                return $this->redirectToRoute('app_games');
            }

            return $this->render('game/show.html.twig', [
                'game' => $game,
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la récupération des données du match: ' . $e->getMessage());
            return $this->redirectToRoute('app_games');
        }
    }
}

