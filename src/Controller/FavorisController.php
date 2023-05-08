<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\User;
use App\Entity\UserFormation;
use App\Form\UserFormationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;
#[Route('/favoris')]
class FavorisController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function getAllFormationFavoris(): array
    {
        $session = new Session(); 
        $session->start(); 
        $u=  $session->get('user');
        $query = $this->entityManager->createQuery(
            'SELECT f FROM App\Entity\UserFormation uf JOIN App\Entity\Formation f WITH uf.idFormation = f.idFormation WHERE uf.idUser = :id'
            )->setParameter('id', 197);

        return $query->getResult();
    }

    public function getAllFormationFavorisMobile(int $id): array
    {

        $query = $this->entityManager->createQuery(
            'SELECT f FROM App\Entity\UserFormation uf JOIN App\Entity\Formation f WITH uf.idFormation = f.idFormation WHERE uf.idUser = :id'
            )->setParameter('id', 200);

        return $query->getResult();
    }

    #[Route('/getallmobile', name: 'app_getAllFormationMobile',methods: ['POST'])]
    public function getAllFormationMobile(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $idf = $data['id'];
        // Do something
        $formations =$this->getAllFormationFavorisMobile($idf);
        $result = [] ;
        foreach ( $formations as $f) {
           $u = [
                'id' => $f->getIdFormation(),
                'name' => $f->getName(),
                'nbh' => $f->getNbh(),
                'nbl' => $f->getNbl(),
           ];
           $result [] = $u ;
        }

        $json = json_encode($result);
        
        $response = new JsonResponse($json, 200, [], true);
        return $response;

    }

    

    #[Route('/', name: 'app_favoris_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $userFormations = $entityManager
            ->getRepository(UserFormation::class)
            ->findAll();

        $session = new Session(); 
        $res = $this->getAllFormationFavoris() ;
        return $this->render('favoris/index.html.twig', [
            'user_formations' => $userFormations,
            'a' => $res,
            'b' => $session->get('id')
        ]);
    }

    #[Route('/new', name: 'app_favoris_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userFormation = new UserFormation();
        $form = $this->createForm(UserFormationType::class, $userFormation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($userFormation);
            $entityManager->flush();

            return $this->redirectToRoute('app_favoris_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('favoris/new.html.twig', [
            'user_formation' => $userFormation,
            'form' => $form,
        ]);
    }

    /*#[Route('/{idUser}', name: 'app_favoris_show', methods: ['GET'])]
    public function show(UserFormation $userFormation): Response
    {
        return $this->render('favoris/show.html.twig', [
            'user_formation' => $userFormation,
        ]);
    }*/

    #[Route('/AddfavorisMobile', name: 'app_favoris_mobile', methods: ['POST'])]
    public function addMobile(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $idf = $data['idFormation'];
        $id = $data['id'];

        $f = $this->entityManager->getRepository(Formation::class)->find($idf);
        $urs = $this->entityManager->getRepository(User::class)->find($id);
        $userFormation = new UserFormation();
        $userFormation->setIdUser($urs);
        $userFormation->setIdFormation($f);

        $entityManager->persist($userFormation);
        $entityManager->flush();

        return new JsonResponse(['success' => 'add to favoris']);
    }


    #[Route('/Addfavoris', name: 'app_favoris_test', methods: ['POST'])]
    public function test(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $session = $request->getSession();
        $u = $session->get('user') ;
        $data = json_decode($request->getContent(), true);

        $id = $data['idFormation'];

        $f = $this->entityManager->getRepository(Formation::class)->find($id);
        $urs = $this->entityManager->getRepository(User::class)->find($u->getIdUser());
        $userFormation = new UserFormation();
        $userFormation->setIdUser($urs);
        $userFormation->setIdFormation($f);

        $entityManager->persist($userFormation);
        $entityManager->flush();

   /*     $accountSid = 'AC8f125b0c6fdcec2626c42735b0955891';
            $authToken = '8cbe05e3c0c31f410e2a1da6ff60dbea';
            $twilioNumber = '+15673339626';
            $client = new Client($accountSid, $authToken);
        
            $message = $client->messages->create(
                '+21622142153', // numéro de téléphone du destinataire
                array(
                    'from' => $twilioNumber,
                    'body' => sprintf(
                        'Formation %s was add by %s on his favoris list',
                        $f->getName(),
                        $urs->getFirstName(),
                    )
                )
            );*/


        return new JsonResponse(['success' => ' '.$u.' '.$id.' '.$f]);
    }

    #[Route('/{idUser}/edit', name: 'app_favoris_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserFormation $userFormation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserFormationType::class, $userFormation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_favoris_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('favoris/edit.html.twig', [
            'user_formation' => $userFormation,
            'form' => $form,
        ]);
    }

    #[Route('/{idUser}', name: 'app_favoris_delete', methods: ['POST'])]
    public function delete(Request $request, UserFormation $userFormation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userFormation->getIdUser(), $request->request->get('_token'))) {
            $entityManager->remove($userFormation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_favoris_index', [], Response::HTTP_SEE_OTHER);
    }
}
