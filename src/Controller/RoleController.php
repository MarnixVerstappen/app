<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/portaal')] // Alles begint met /portaal/...
class RoleController extends AbstractController
{
    // 1. Pagina voor Verantwoordelijk
    #[Route('/verantwoordelijk', name: 'app_verantwoordelijk')]
    #[IsGranted('ROLE_VERANTWOORDELIJK')]
    public function verantwoordelijk(): Response
    {
        return $this->render('roles/verantwoordelijk.html.twig');
    }

    // 2. Pagina voor Uitvoerend
    #[Route('/uitvoerend', name: 'app_uitvoerend')]
    #[IsGranted('ROLE_UITVOEREND')]
    public function uitvoerend(): Response
    {
        return $this->render('roles/uitvoerend.html.twig');
    }

    // 3. Pagina voor Raadplegen
    #[Route('/raadplegen', name: 'app_raadplegen')]
    #[IsGranted('ROLE_RAADPLEGEN')]
    public function raadplegen(): Response
    {
        return $this->render('roles/raadplegen.html.twig');
    }

    // 4. Pagina voor Informeren
    #[Route('/informeren', name: 'app_informeren')]
    #[IsGranted('ROLE_INFORMEREN')]
    public function informeren(): Response
    {
        return $this->render('roles/informeren.html.twig');
    }

    // 5. Pagina voor Support
    #[Route('/support', name: 'app_support')]
    #[IsGranted('ROLE_SUPPORT')]
    public function support(): Response
    {
        return $this->render('roles/support.html.twig');
    }
}