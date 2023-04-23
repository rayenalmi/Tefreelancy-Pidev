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

    public function myFunction(): Response
    {
        // Do something

        // Return a response
        return $this->render('formation/index.html.twig', [
            'message' => 'Hello, world!',
        ]);
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

    #[Route('/', name: 'app_formation_index', methods: ['GET','POST'])]
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

    public function getFormationByName(string $name): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT u FROM App\Entity\Formation u WHERE u.name = :name'
        )->setParameter('name', $name);

        return $query->getResult();
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

    #[Route('/{idFormation}', name: 'app_formation_show', methods: ['GET'])]
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
