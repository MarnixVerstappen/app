<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\ProjectMember;
use App\Form\ProjectMemberType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_VERANTWOORDELIJK')]
class ProjectMemberController extends AbstractController
{
    // Let op: Ik heb de URL aangepast naar /project/{id}/members
    // Hierdoor weet Symfony voor welk project we bezig zijn.
    #[Route('/project/{id}/members', name: 'app_project_members')]
    public function index(Project $project, Request $request, EntityManagerInterface $entityManager): Response
    {
        $member = new ProjectMember();
        $member->setProject($project);

        $form = $this->createForm(ProjectMemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($member);
            $entityManager->flush();

            $this->addFlash('success', 'Medewerker toegevoegd!');

            return $this->redirectToRoute('app_project_members', ['id' => $project->getId()]);
        }

        // We gebruiken de template die het commando voor je heeft gemaakt:
        return $this->render('project_member/index.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
            'members' => $project->getProjectMembers(),
        ]);
    }
}