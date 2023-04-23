<?php

namespace App\Controller;

use BaconQrCode\Renderer\ImageRenderer;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Offer;


class TestController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/test', name: 'app_test')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $offers = $entityManager->getRepository(Offer::class)->findAll();
        $data = [];
        foreach ($offers as $offer) {
            $data[] = [
                'name' => $offer->getName(),
                'salary' => $offer->getSalary()
            ];
        }
        //$ch = json_encode($data);
        //echo($ch);
        //die;

        return $this->render('test/index.html.twig', [
            'data' => json_encode($data),
            'offers' => $offers
        ]);
    }

    #[Route('/pdf/{id}', name: 'PDF_offer', methods: ['GET'])]
    public function pdf(EntityManagerInterface $entityManager, $id)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Open Sans');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $offer = $entityManager->getRepository(Offer::class)->find($id);
        $html = $this->renderView('test/pdf.html.twig', [
            'offer' => $offer,
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
            'Content-Disposition' => 'attachment; filename="Offer.pdf"',
        ]);

        return $response;
    }

    public function findOfferByName($name)
    {
        $query = $this->$entityManager->createQuery(
            'SELECT o FROM App\Entity\Offer o WHERE o.name LIKE :name'
        )->setParameter('name', '%' . $name . '%');

        return $query->getResult();
    }


    #[Route('/recherche_offre_ajax', name: 'recherche_offre_ajax')]
    public function rechercheOffreAjax(Request $request): JsonResponse
    {
        $requestString = $request->query->get('searchValue');
        $resultats = $this->findOfferByName($requestString);

        return $this->json($resultats);
    }
}
