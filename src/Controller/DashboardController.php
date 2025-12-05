<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(ProjectRepository $projectRepository): Response
    {
        // 1. Haal alle projecten op uit de database (nieuwste bovenaan)
        $projects = $projectRepository->findBy([], ['id' => 'DESC']);

        // 2. Stuur de projecten-lijst mee naar de pagina
        return $this->render('dashboard/index.html.twig', [
            'projects' => $projects,
        ]);
    }
}