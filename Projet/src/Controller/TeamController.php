<?php

namespace App\Controller;

use App\Service\NbaApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/team')]
#[IsGranted('ROLE_USER')]
class TeamController extends AbstractController
{
    public function __construct(
        private NbaApiService $nbaApiService
    ) {}

    #[Route('/{id}', name: 'app_team_show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        try {
            $team = $this->nbaApiService->getTeam($id);
            
            if (isset($team['error'])) {
                $this->addFlash('error', 'Erreur: ' . $team['error']);
                return $this->redirectToRoute('app_teams');
            }

            $players = $this->nbaApiService->getPlayersByTeam($id);

            return $this->render('team/show.html.twig', [
                'team' => $team,
                'players' => $players['data'] ?? [],
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la récupération des données de l\'équipe: ' . $e->getMessage());
            return $this->redirectToRoute('app_teams');
        }
    }
}

