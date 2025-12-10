<?php

namespace App\Controller;

use App\Entity\Cell;
use App\Entity\Project;
use App\Entity\Sheet;
use App\Service\ExcelColorHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelController extends AbstractController
{
    #[Route('/excel', name: 'app_excel')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $sheetsData = []; // This will hold ALL your tabs

        if ($request->isMethod('POST')) {
            $file = $request->files->get('excel_file');

            if ($file) {
                try {
                    $spreadsheet = IOFactory::load($file->getPathname());

                    $projectTitle = $file->getClientOriginalName();
                    // strip the .xls, .xslx or .xlsm extension
                    $projectTitle = preg_replace('/\.(xls|xlsx|xlsm)$/i', '', $projectTitle);

                    $project = new Project();
                    $project->setTitle($projectTitle);
                    $project->setStartDate(new \DateTime());
                    $em->persist($project);

                    // Loop through every single tab
                    foreach ($spreadsheet->getAllSheets() as $sheet) {
                        $sheetName = $sheet->getTitle();
                        $sheetsData[$sheetName] = $sheet->toArray(null, true, true, true);

                        // TODO: create Sheet entity
                        $sheetObject = new Sheet();
                        $sheetObject->setTitle($sheetName);
                        $sheetObject->setProject($project);
                        $em->persist($sheetObject);

                        // TODO: process content of $sheetsData
                        // put each cell in each row in the database with an identifier

                        // IMPORTANT: Make sure to link everything to the correct sheet/tab
                        // every cell must have a coordinate and be linked to $sheetName

                        $i = 0;
                        // for each row in the sheet
                        foreach ($sheet->getRowIterator() as $row) {
                            // activate cell iterator
                            $cellIterator = $row->getCellIterator();
                            // with this you don't process empty cells
                            $cellIterator->setIterateOnlyExistingCells(true);
                            $colorHelper = new ExcelColorHelper();
                            
                            foreach ($cellIterator as $cell) {
                                if ($cell->getValue() !== '' && $cell->getValue() !== null) {
                                    // getCoordinate returns something like "B3", and getValue the content
                                    // this content might need to be decrypted or sanitized before saving to the database
                                    $rawValue = $cell->getValue();

                                    // 1. Sanitize: Replace NBSP with normal space, then trim
                                    //    We use ?? '' to handle nulls safely in one line
                                    $cleanValue = trim(str_replace("\u{A0}", ' ', (string)$rawValue));

                                    // get style attributes from the cell if needed

                                    $style = $cell->getStyle();

                                    $fillColor = $colorHelper->resolveFillColor($style->getFill(), $cleanValue);

                                    $cellObject = new Cell();
                                    $cellObject->setCoordinate($cell->getCoordinate());
                                    $cellObject->setValue($cleanValue);
                                    $cellObject->setSheet($sheetObject);
                                    $cellObject->setFill($fillColor);   // Now handles ARGB correctly
                                    $cellObject->setColor($style->getFont()->getColor()->getRGB()); // Now handles ARGB correctly
                                    $cellObject->setBold($style->getFont()->getBold());   // Now detects "Partial" bold
                                    $em->persist($cellObject);
                                }
                            }
                        }
                    }
                    // Finally flush all at once
                    $em->flush();
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