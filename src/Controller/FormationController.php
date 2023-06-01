<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Chapters;

use App\Form\FormationType;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/formation')]
class FormationController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findFormation($name)
    {
       /* var_dump($keyword); 
        die()*/ ; //transforme array to string 
        //$ch = implode('', $keyword);
        $query = $this->entityManager
        ->createQuery(
            'SELECT o
            FROM App\Entity\Formation o
            WHERE o.name LIKE  :name')
        ->setParameter('name', '%'.$name.'%' );

        return $query->getResult();
    }

    #[Route('/recherche_ajax', name: 'recherche_ajax_formation')]
    public function rechercheAjax(Request $request): JsonResponse
    {
        $requestString = $request->query->get('searchValue');
        
        $resultats = $this->entityManager
        ->createQuery(
            'SELECT o
            FROM App\Entity\Formation o
            WHERE o.name LIKE  :name')
        ->setParameter('name', '%'.$requestString.'%' )
        ->getResult();
        return $this->json($resultats);
    }

    
    #[Route('/back', name: 'app_formation_back', methods: ['GET','POST'])]
    public function indexbackOffice(Request $req,EntityManagerInterface $entityManager): Response
    {
        $formations = $entityManager
            ->getRepository(Formation::class)
            ->findAll();
        $chapters = $entityManager
            ->getRepository(Chapters::class)
            ->findAll();

        $form= $this->createForm(SearchType::class);
        $form->handleRequest($req);

        if($form->isSubmitted()){

            $f =strlen($form["search"]->getData()) ==0  ? $formations : $this->findFormation($form["search"]->getData());
            
            return $this->render('formation/index.html.twig', [
                'formations' => $f,
                'chapters' => $chapters,
                'form'=>$form->createView()
            ]);
        
        }

        return $this->render('formation/backformation.html.twig', [
            'formations' => $formations,
            'chapters' => $chapters,
            'form'=>$form->createView()
        ]);
    }

    #[Route('/getall', name: 'app_formation_index', methods: ['GET','POST'])]
    public function index(Request $req,EntityManagerInterface $entityManager): Response
    {
        $formations = $entityManager
            ->getRepository(Formation::class)
            ->findAll();
        $chapters = $entityManager
            ->getRepository(Chapters::class)
            ->findAll();

        $form= $this->createForm(SearchType::class);
        $form->handleRequest($req);

        if($form->isSubmitted()){

            $f =strlen($form["search"]->getData()) ==0  ? $formations : $this->findFormation($form["search"]->getData());
            
            return $this->render('formation/index.html.twig', [
                'formations' => $f,
                'chapters' => $chapters,
                'form'=>$form->createView()
            ]);
        
        }

        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
            'chapters' => $chapters,
            'form'=>$form->createView()
        ]);
    }

    public function getAllFormationFavorisMobile(int $id): array
    {

        $query = $this->entityManager->createQuery(
            'SELECT f FROM App\Entity\UserFormation uf JOIN App\Entity\Formation f WITH uf.idFormation = f.idFormation WHERE uf.idUser = :id'
            )->setParameter('id', $id);

        return $query->getResult();
    }

    #[Route('/getallmobilefav', name: 'app_qxQXqxqXnMobile',methods: ['POST'])]
    public function getAllFormationMobileFav(Request $request)
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

    #[Route('/getallmobile', name: 'app_getAllFormationMobile')]
    public function getAllFormationMobile(EntityManagerInterface $entityManager)
    {
        // Do something
        $formations =$entityManager
        ->getRepository(Formation::class)
        ->findAll();
        //var_dump($freelancers);
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

    #[Route('/getallchptersmobile', name: 'app_getchaptersMobile')]
    public function getAllchaptersFormationMobile(EntityManagerInterface $entityManager , Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $idf = $data['id'];
        // Do something
        $chapters =$this->entityManager
        ->createQuery(
            'SELECT c
            FROM App\Entity\Chapters c
            WHERE c.formation = :formation')
        ->setParameter('formation', $idf )
        ->getResult();
        $result = [] ;
        foreach ( $chapters as $c) {
           $u = [
                'id' => $c->getId(),
                'name' => $c->getName(),
                'context' => $c->getContext(),
           ];
           $result [] = $u ;
        }

        $json = json_encode($result);
        
        $response = new JsonResponse($json, 200, [], true);
        return $response;

    }

    #[Route('/chaptersLinkTo/{idFormation}', name: 'app_chapters_link_to_formation',methods: ['GET', 'POST'])]
    public function chaptersLinkToFormation(Formation $formation): Response
    {
        // Do something
        $resultats = $this->entityManager
        ->createQuery(
            'SELECT c
            FROM App\Entity\Chapters c
            WHERE c.formation = :formation')
        ->setParameter('formation', $formation->getIdFormation() )
        ->getResult();

        // Return a response
        return $this->render('formation/chaptersLinkToFormation.html.twig', [
            'formation' => $formation,
            'chapters' => $resultats,
            'message' => 'Hello, world!',
        ]);
    }

    public function getFormationByName(string $name): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT u FROM App\Entity\Formation u WHERE u.name = :name'
        )->setParameter('name', $name);

        return $query->getResult();
    }

        

    #[Route('/newFormationMobile', name: 'app_formation_new', methods: ['POST'])]
    public function newFormation(Request $request, EntityManagerInterface $entityManager)
    {
        $formation = new Formation();
        $data = json_decode($request->getContent(), true);

        $name = $data['name'];
        $nbl = $data['nbl'];
        $nbh = $data['nbh'];
        
        $formation->setName($name);
        $formation->setNbh($nbh);
        $formation->setNbl($nbl);
        $formation->setPath('http//test.html');
        
        $entityManager->persist($formation);
        $entityManager->flush();

        return new JsonResponse(["root" => "formation added"]);

    }

    #[Route('/new', name: 'app_formation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $u = $this->getFormationByName($form["name"]->getData());
            if (count($u)!=0) 
            {   
                $this->addFlash('error', 'Your action!');
                return $this->redirectToRoute('app_formation_new', [], Response::HTTP_SEE_OTHER);
            }

            $entityManager->persist($formation);
            $entityManager->flush();

            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{idFormation}', name: 'app_wormation_show', methods: ['GET'])]
    public function show(Formation $formation): Response
    {
        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }

    

    #[Route('/{idFormation}/edit', name: 'app_formation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{idFormation}', name: 'app_formation_delete', methods: ['POST'])]
    public function delete(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formation->getIdFormation(), $request->request->get('_token'))) {
            $entityManager->remove($formation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
    }
}
