<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(): Response
    {
        // 1. Check if user is logged in (Security check)
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // 2. Get the current user
        $user = $this->getUser();

        // 3. Option: You could redirect specific roles to totally different pages here
        // if ($this->isGranted('ROLE_ADMIN')) {
        //     return $this->render('dashboard/admin.html.twig');
        // }

        // 4. For now, we render ONE nice dashboard that changes based on the user
        return $this->render('dashboard/index.html.twig', [
            'user_firstname' => $user->getUserIdentifier(), // Uses the firstname we set up earlier
        ]);
    }
}
