<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Form\OfferType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\SearchOfferType;


#[Route('/start/offer')]
class OfferController extends AbstractController

{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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

    

    #[Route('/', name: 'app_offer_index', methods: ['GET','POST'])]
    public function index(Request $req,EntityManagerInterface $entityManager): Response
    {
            $offers = $entityManager->getRepository(Offer::class)->findAll();;
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
        $offer = new Offer();
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
        $form = $this->createForm(OfferType::class, $offer);
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

    

    
}
