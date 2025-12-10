<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\ProjectTask;
use App\Form\ProjectType;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_VERANTWOORDELIJK')]
class ProjectController extends AbstractController
{
    #[Route('/project/new', name: 'app_project_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $excelFile = $form->get('excelFile')->getData();

            if ($excelFile) {
                try {
                    $spreadsheet = IOFactory::load($excelFile->getPathname());
                    $sheet = $spreadsheet->getActiveSheet();

                    $highestRow = $sheet->getHighestRow();
                    for ($row = 9; $row <= $highestRow; $row++) {
                        $description = $sheet->getCell('C' . $row)->getValue();

                        if (!empty($description)) {
                            $task = new ProjectTask();
                            $task->setDescription((string)$description);
                            $task->setStatus('Open');
                            $task->setProject($project);
                            $entityManager->persist($task);
                        }
                    }
                } catch (\Exception $e) {
                    $this->addFlash('danger', 'Fout bij lezen Excel: ' . $e->getMessage());
                    return $this->redirectToRoute('app_project_new');
                }
            }

            $entityManager->persist($project);
            $entityManager->flush();

            $this->addFlash('success', 'Project succesvol aangemaakt!');

            // LET OP: Na aanmaken sturen we je nu direct naar de detailpagina!
            return $this->redirectToRoute('app_project_show', ['id' => $project->getId()]);
        }

        return $this->render('project/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // --- NIEUW GEDEELTE HIERONDER ---

    #[Route('/project/{id}', name: 'app_project_show')]
    public function show(Project $project): Response
    {
        // We need to prepare a "View Model" for each sheet
        $sheetsData = [];

        foreach ($project->getSheets() as $sheet) {
            $grid = [];
            $maxRow = 0;
            $maxCol = 0;

            foreach ($sheet->getCells() as $cell) {
                // Convert "B2" -> Column "B", Row 2
                [$colStr, $rowIndex] = Coordinate::coordinateFromString($cell->getCoordinate());

                // Convert "B" -> 2 (integer index)
                $colIndex = Coordinate::columnIndexFromString($colStr);

                // Populate the grid: grid[row][col] = Cell Entity
                $grid[$rowIndex][$colIndex] = $cell;

                // Track boundaries
                if ($rowIndex > $maxRow) $maxRow = $rowIndex;
                if ($colIndex > $maxCol) $maxCol = $colIndex;
            }

            // Generate headers (A, B, C...) up to the max column
            $headers = [];
            for ($c = 1; $c <= $maxCol; $c++) {
                $headers[$c] = Coordinate::stringFromColumnIndex($c);
            }

            $sheetsData[] = [
                'entity' => $sheet,
                'grid'   => $grid,
                'maxRow' => $maxRow,
                'headers'=> $headers,
            ];
        }

        return $this->render('project/show.html.twig', [
            'project' => $project,
            'sheetsData' => $sheetsData,
        ]);
    }
}