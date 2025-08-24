<?php

namespace App\Controller;

use App\Service\NbaApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/player')]
#[IsGranted('ROLE_USER')]
class PlayerController extends AbstractController
{
    public function __construct(
        private NbaApiService $nbaApiService
    ) {}

    #[Route('/{id}', name: 'app_player_show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        try {
            $player = $this->nbaApiService->getPlayer($id);
            
            if (isset($player['error'])) {
                $this->addFlash('error', 'Erreur: ' . $player['error']);
                return $this->redirectToRoute('app_players');
            }

            // Debug: afficher la structure des données
            if ($this->getParameter('kernel.environment') === 'dev') {
                $this->addFlash('info', 'Structure des données: ' . json_encode(array_keys($player)));
            }

            return $this->render('player/show.html.twig', [
                'player' => $player,
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la récupération des données du joueur: ' . $e->getMessage());
            return $this->redirectToRoute('app_players');
        }
    }
}

