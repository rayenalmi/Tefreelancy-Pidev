<?php

namespace App\Controller;

use App\Entity\Candidacy;
use App\Entity\Offer;
use App\Entity\User;
use App\Form\CandidacyType;
use App\Form\CandidacyType1;
use App\Form\CandidacyTypeEdit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;

#[Route('/start/candidacy')]
class CandidacyController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }




            
    public function getCandidacyByID(int $id): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT c.idCandidacy,c.object,c.message,c.accepted FROM App\Entity\Candidacy c  WHERE c.idFreelancer = :id'
        )->setParameter('id', $id);

        return $query->getResult();
    }

    public function getCandidacyOfRecruter(int $id): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT o FROM App\Entity\Offer o  WHERE c.idFreelancer = :id'
        )->setParameter('id', $id);

        return $query->getResult();
    }

    #[Route('/updateCondidacyTrue', name: 'updateCondidacy_true', methods: ['PUT'])]
    public function rechercheAjadx(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $idcandidacy = $data['idCandidacy'];

        
        $qb = $this->entityManager->createQueryBuilder();
        $accepted = true ; 
        //$idcandidacy =$c->getIdCandidacy();
        $qb->update(Candidacy::class, 'c')
            ->set('c.accepted', ':accepted')
            ->where('c.idCandidacy = :id')
            ->setParameter('accepted', $accepted)
            ->setParameter('id', $idcandidacy);

            $updatedRows = $qb->getQuery()->execute();

            $response = new JsonResponse(['updatedRows' => $updatedRows]);
            
            return new JsonResponse(['updatedRows' => $idcandidacy]);
    }

    #[Route('/updateCondidacyFalse', name: 'updateCondidacy_false', methods: ['PUT'])]
    public function rechercheAjadx2(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $idcandidacy = $data['idCandidacy'];

        
        $qb = $this->entityManager->createQueryBuilder();
        $accepted = false ; 
        //$idcandidacy =$c->getIdCandidacy();
        $qb->update(Candidacy::class, 'c')
            ->set('c.accepted', ':accepted')
            ->where('c.idCandidacy = :id')
            ->setParameter('accepted', $accepted)
            ->setParameter('id', $idcandidacy);

            $updatedRows = $qb->getQuery()->execute();

            $response = new JsonResponse(['updatedRows' => $updatedRows]);
            
            return new JsonResponse(['updatedRows' => $idcandidacy]);
    }

    #[Route('/recruter', name: 'app_candidacy_recruter', methods: ['GET'])]
    public function indexRecruter(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $u = $session->get('user');  
        $candidacies = $this->getCandidacyByID($u->getIdUser());

        return $this->render('candidacy/index.html.twig', [
            'candidacies' => $candidacies,
        ]);
    }


    public function getAllCandidacyOnOneOffer(int $id): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT c.idCandidacy , u.photo  ,u.email , c.object , c.message FROM App\Entity\Candidacy c JOIN App\Entity\User u WITH c.idFreelancer = u.idUser WHERE c.idOffer = :id AND c.accepted IS NULL '
        )->setParameter('id', $id);

        return $query->getResult();
    }

    #[Route('/recruter/{idOffer}', name: 'getCandidacyByOffer', methods: ['GET'])]
    public function getAllCandidacyByOffer(Offer $o , Request $request, EntityManagerInterface $entityManager): Response
    {
        
        $candidacies = $this->getAllCandidacyOnOneOffer($o->getIdOffer());
       // var_dump($candidacies);
        $e = json_encode($candidacies) ; 
        $data = json_decode($e, true);
        

        return $this->render('candidacy/candidacyByOffer.html.twig', [
            'candidacies' => $data,
            'offer' => $o
        ]);
    }


    #[Route('/', name: 'app_candidacy_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $u = $session->get('user');
        
        $candidacies = $this->getCandidacyByID($u->getIdUser());
        var_dump($candidacies[0]["accepted"]);

        foreach ($candidacies as $c) {

            if (is_object($c) && method_exists($c, 'getAccepted')) {
                var_dump($c->getAccepted());
            }
        }
        
        return $this->render('candidacy/index.html.twig', [
            'candidacies' => $candidacies,
        ]);
    }

    public  function sms(){
        // Your Account SID and Auth Token from twilio.com/console
                $sid = 'ACb111614e8a65dfa5310d91c478b19542';
                $auth_token = '1eeca4d7c867fdc7debd991932a7fce6';
        // In production, these should be environment variables. E.g.:
        // $auth_token = $_ENV["TWILIO_AUTH_TOKEN"]
        // A Twilio number you own with SMS capabilities
                $twilio_number = "+12764962398";
        
                $client = new Client($sid, $auth_token);
                $client->messages->create(
                // the number you'd like to send the message to
                    '+21692661515',
                    [
                        // A Twilio phone number you purchased at twilio.com/console
                        'from' => '+12764962398',
                        // the body of the text message you'd like to send
                        'body' => 'votre candidature est envoyée avec succés'
                    ]
                );
            }

    #[Route('/new/{idOffer}', name: 'app_candidacy_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,Offer $offer): Response
    {
        
        $session = $request->getSession();
        $u = $session->get('user');
        $urs = $this->entityManager->getRepository(User::class)->find($u->getIdUser());
        $candidacy = new Candidacy();
        $candidacy->setIdFreelancer($urs);
        $candidacy->setIdOffer($offer);
        
        $form = $this->createForm(CandidacyType1::class, $candidacy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($candidacy);
            $entityManager->flush();
            // Send an SMS notification to the freelancer  
            //$this->sms();
            $this->addFlash('danger', 'reponse envoyée avec succées');

            return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('candidacy/new.html.twig', [
            'candidacy' => $candidacy,
            'form' => $form,
        ]);
    }

    #[Route('/{idCandidacy}', name: 'app_candidacy_show', methods: ['GET'])]
    public function show(Candidacy $candidacy): Response
    {
        return $this->render('candidacy/show.html.twig', [
            'candidacy' => $candidacy,
        ]);
    }

    #[Route('/{idCandidacy}/edit', name: 'app_candidacy_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Candidacy $candidacy, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CandidacyTypeEdit::class, $candidacy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_candidacy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('candidacy/edit.html.twig', [
            'candidacy' => $candidacy,
            'form' => $form,
        ]);
    }

    #[Route('/{idCandidacy}', name: 'app_candidacy_delete', methods: ['POST'])]
    public function delete(Request $request, Candidacy $candidacy, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $candidacy->getIdCandidacy(), $request->request->get('_token'))) {
            $entityManager->remove($candidacy);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_candidacy_index', [], Response::HTTP_SEE_OTHER);
    }
}
