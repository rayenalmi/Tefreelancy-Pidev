<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Form\OfferType;
use App\Form\OfferTypeEdit;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\SearchOfferType;
use Dompdf\Dompdf;
use Dompdf\Options;


#[Route('/start/offer')]
class OfferController extends AbstractController

{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getOfferByID(int $id): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT o.idOffer,o.name,o.description,o.duration,o.keywords,o.salary FROM App\Entity\Offer o  WHERE o.idRecruter = :id'
        )->setParameter('id', $id);

        return $query->getResult();
    }
    public function getOfferByName(string $name): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT o FROM App\Entity\Offer o WHERE o.name = :name'
        )->setParameter('name', $name);

        return $query->getResult();
    }

    public function RechercheDesc(): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT o
            FROM App\Entity\Offer o
            ORDER BY o.name DESC'
        );

        return $query->getResult();
    }
    public function RechercheAsc(): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT o
            FROM App\Entity\Offer o
            ORDER BY o.name ASC'
        );

        return $query->getResult();
    }

    
    public function findOffer($keyword)
    {
       /* var_dump($keyword); 
        die()*/ ; //transforme array to string 
        $ch = implode('', $keyword);
        $query = $this->entityManager
        ->createQuery(
            'SELECT o
            FROM App\Entity\Offer o
            WHERE o.keywords LIKE  :keywords')
        ->setParameter('keywords', '%'.$ch.'%' );

        return $query->getResult();
    }


    #[Route('/RecruterOffer', name: 'app_offer_index_recruter', methods: ['GET','POST'])]
    public function indexRecruter(Request $req,EntityManagerInterface $entityManager): Response
    {
        $session = $req->getSession();
        $u = $session->get('user');

            $offers = $this->getOfferByID($u->getIdUser());
            $form= $this->createForm(SearchOfferType::class);
            $form->handleRequest($req);        
            if($form->isSubmitted()){
            
                /*var_dump($this); 
                die() ;*/ 
                $offers = $this->findOffer($form->getData('search'));
                
                return $this->render('offer/index_recruter.html.twig', [
                    'offers'=>$offers,
                    'form'=>$form->createView()
                ]);
            }

        return $this->render('offer/index_recruter.html.twig', [
            'offers' => $offers,
            'form'=>$form->createView()
        ]);
    }

    

    #[Route('/', name: 'app_offer_index', methods: ['GET','POST'])]
    public function index(Request $req,EntityManagerInterface $entityManager): Response
    {
            $offers = $entityManager->getRepository(Offer::class)->findAll();
            /*$offers = $paginator->paginate(
                $offers,
                $req->query->getInt(key:'page',default:1),
                limit:2
            );*/
            $form= $this->createForm(SearchOfferType::class);
            $form->handleRequest($req);        
            if($form->isSubmitted()){
            
                /*var_dump($this); 
                die() ;*/ 
                $offers = $this->findOffer($form->getData('search'));
                
                return $this->render('offer/index.html.twig', [
                    'offers'=>$offers,
                    'form'=>$form->createView()
                ]);
            }

        return $this->render('offer/index.html.twig', [
            'offers' => $offers,
            'form'=>$form->createView()
        ]);
    }

    #[Route('/new', name: 'app_offer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $u = $session->get('user');
        $offer = new Offer();
        $offer->setIdRecruter($u);
        $form = $this->createForm(OfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $x=$this->getOfferByName($form["name"]->getData());
            if(count($x)!=0){
                $this->addFlash('error','alerte!');
                return $this->redirectToRoute('app_offer_new', [], Response::HTTP_SEE_OTHER);
            }
            $entityManager->persist($offer);
            $entityManager->flush();

            return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('offer/new.html.twig', [
            'offer' => $offer,
            'form' => $form,
        ]);
    }

    #[Route('/{idOffer}', name: 'app_offer_show', methods: ['GET'])]
    public function show(Offer $offer): Response
    {
        return $this->render('offer/show.html.twig', [
            'offer' => $offer,
        ]);
    }

    #[Route('/{idOffer}/edit', name: 'app_offer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offer $offer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OfferTypeEdit::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('offer/edit.html.twig', [
            'offer' => $offer,
            'form' => $form,
        ]);
    }

    #[Route('/{idOffer}', name: 'app_offer_delete', methods: ['POST'])]
    public function delete(Request $request, Offer $offer, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offer->getIdOffer(), $request->request->get('_token'))) {
            $entityManager->remove($offer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/pdf/{id}', name: 'PDF_offer', methods: ['GET'])]
    public function pdf(EntityManagerInterface $entityManager,$id)
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



    #[Route('/yas', name: 'app_yass')]
    function helloWorld(): Response
{
    return new Response('Hello, world!');
}

}
