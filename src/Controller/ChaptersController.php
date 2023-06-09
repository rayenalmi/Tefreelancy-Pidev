<?php

namespace App\Controller;

use App\Entity\Chapters;
use App\Entity\Formation;
use App\Form\ChaptersType;
use App\Form\ChapterLinkType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/chapters')]
class ChaptersController extends AbstractController
{
    #[Route('/', name: 'app_chapters_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $chapters = $entityManager
            ->getRepository(Chapters::class)
            ->findAll();

        return $this->render('chapters/index.html.twig', [
            'chapters' => $chapters,
        ]);
    }

    #[Route('/new', name: 'app_chapters_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chapter = new Chapters();
        $form = $this->createForm(ChaptersType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chapter);
            $entityManager->flush();

            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('chapters/new.html.twig', [
            'chapter' => $chapter,
            'form' => $form,
        ]);
    }

    #[Route('/new/chap/linkto/{id}', name: 'app_chapters_newLinkTo', methods: ['GET', 'POST'])]
    public function newLinkToFormation(Request $request, EntityManagerInterface $entityManager, Formation $formation): Response
    {
        $chapter = new Chapters();
        $chapter->setFormation($formation);
        $form = $this->createForm(ChapterLinkType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chapter);
            $entityManager->flush();

            return $this->redirectToRoute('app_formation_back', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('chapters/new.html.twig', [
            'chapter' => $chapter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chapters_show', methods: ['GET'])]
    public function show(Chapters $chapter): Response
    {
        return $this->render('chapters/show.html.twig', [
            'chapter' => $chapter,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_chapters_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chapters $chapter, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChaptersType::class, $chapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_formation_back', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('chapters/edit.html.twig', [
            'chapter' => $chapter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chapters_delete', methods: ['POST'])]
    public function delete(Request $request, Chapters $chapter, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chapter->getId(), $request->request->get('_token'))) {
            $entityManager->remove($chapter);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_formation_back', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/pdf/{id}', name: 'PDF_chapter', methods: ['GET'])]
    public function pdf(EntityManagerInterface $entityManager, $id)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Open Sans');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $chapter = $entityManager->getRepository(Chapters::class)->find($id);
        $html = $this->renderView('chapters/pdf.html.twig', [
            'chapter' => $chapter,
        ]);

        // Add header HTML to $html variable
        $headerHtml = '<h1 style="text-align: center; color: #b0f2b6;">Bienvenue chez Freelancy </h1>';
        $html = $headerHtml . $html;

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        // Send the PDF to the browser
        $response = new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="chapter.pdf"',
        ]);

        return $response;
    }
}
