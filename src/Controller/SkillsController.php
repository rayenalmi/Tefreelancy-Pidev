<?php

namespace App\Controller;

use App\Entity\PropertySearchSkill;
use App\Entity\Skills;
use App\Form\PropertySearchSkillType;
use App\Form\SkillsType;
use App\Repository\SkillsRepository; 
use App\Repository\PropertyRepository; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/skills')]
class SkillsController extends AbstractController

{
/*
private EntityManagerInerface $entityManager; 
public function __construct(EntityManagerInterface $entityManager)
{
    $this->entityManager = $entityManager; 
}
 */



 #[Route('/f', name: 'app_skills_byfreelancer', methods: ['GET'])]
    //public function getfreelancer($id, EntityManagerInterface $entityManager): Response
    public function getf( SkillsRepository $repo, Request $request, PaginatorInterface $paginator): Response
    
    {

        $session = $request->getSession();
        $u =  $session->get('user'); 
        //$experiences = $repo->findByFreelancerId($u->getIdUser()); 
        //$skills = $repo->findByFreelancerId($id); 
        
        $pagination = $paginator->paginate(
            $repo->paginationQuery($u->getIdUser()),
            $request->query->get('page',1),
            2
        ); 


        return $this->render('skills/index.html.twig', [
            //'skills' => $skills,
            'pagination' => $pagination
        ]);
    }

    // search & filter 
    
    #[Route('/search', name: 'search_skills_1', methods: ['GET'])]
    public function searchSkills( Request $request,ManagerRegistry $doctrine, SkillsRepository $repo2): Response
    {


        $session = $request->getSession();
        $u =  $session->get('user'); 
        //$experiences = $repo->findByFreelancerId($u->getIdUser()); 

        $em = $doctrine->getManager(); 
        //$skills = $em->getRepository(Skills::class)->findAll();
        //$skills = $em->getRepository(Skills::class)->findByFreelancerId($id);
        $skills = $repo2->findByFreelancerId($u->getIdUser()); 

        $search = $request->query->get('search');

 // if search query is set, get the search results and pass them to the view
        if ($search) {
            $skills = $repo2->findBySearchQuery($search, $u->getIdUser());
        }

       
        return $this->render('search/skills.html.twig', [
        
            'skills' => $skills,
    
        ]);
    }






    #[Route('/', name: 'app_skills_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $skills = $entityManager
            ->getRepository(Skills::class)
            ->findAll();

        return $this->render('skills/index.html.twig', [
            'skills' => $skills,
        ]);
    }

    #[Route('/new', name: 'app_skills_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $skill = new Skills();
        $form = $this->createForm(SkillsType::class, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($skill);
            $entityManager->flush();

            return $this->redirectToRoute('app_skills_byfreelancer', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('skills/new.html.twig', [
            'skill' => $skill,
            'form' => $form,
        ]);
    }








    #[Route('/{idSkills}', name: 'app_skills_show', methods: ['GET'])]
    public function show(Skills $skill): Response
    {
        return $this->render('skills/show.html.twig', [
            'skill' => $skill,
        ]);
    }

    #[Route('/{idSkills}/edit', name: 'app_skills_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Skills $skill, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SkillsType::class, $skill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_skills_byfreelancer', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('skills/edit.html.twig', [
            'skill' => $skill,
            'form' => $form,
        ]);
    }

    #[Route('/{idSkills}', name: 'app_skills_delete', methods: ['POST'])]
    public function delete(Request $request, Skills $skill, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$skill->getIdSkills(), $request->request->get('_token'))) {
            $entityManager->remove($skill);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_skills_byfreelancer', [], Response::HTTP_SEE_OTHER);
    }



    public function findByFreelancerId($value): array
        {
            return $this->createQueryBuilder('s')
                ->andWhere('s.idFreelancer = :val')
                ->setParameter('val', $value)
                //->orderBy('s.id', 'ASC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult()
            ;
        }

    



    #[Route('/recherche_ajax', name: 'recherche_ajax_skills')]
    public function rechercheAjax(Request $request): JsonResponse
    {
        $requestString = $request->query->get('searchValue');
        
        $resultats = $this->entityManager
        ->createQuery(
            'SELECT t
            FROM App\Entity\Skills s
            WHERE s.name LIKE  :name')
        ->setParameter('name', '%'.$requestString.'%' )
        ->getArrayResult();
        return $this->json($resultats);
    }



}
