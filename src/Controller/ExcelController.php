<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelController extends AbstractController
{
    #[Route('/excel', name: 'app_excel')]
    public function index(Request $request): Response
    {
        $sheetsData = []; // This will hold ALL your tabs

        if ($request->isMethod('POST')) {
            $file = $request->files->get('excel_file');

            if ($file) {
                try {
                    $spreadsheet = IOFactory::load($file->getPathname());

                    // Loop through every single tab
                    foreach ($spreadsheet->getAllSheets() as $sheet) {
                        $sheetName = $sheet->getTitle();
                        $sheetsData[$sheetName] = $sheet->toArray(null, true, true, true);
                    }

                } catch (\Exception $e) {
                    $this->addFlash('danger', 'Error reading file: ' . $e->getMessage());
                }
            }
        }

        return $this->render('excel/index.html.twig', [
            'sheetsData' => $sheetsData, // <--- FIXED: Now matches the name used in your HTML file
        ]);
    }
}