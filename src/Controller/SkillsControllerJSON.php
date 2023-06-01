<?php

namespace App\Controller;
use App\Entity\User;
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
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


#[Route('/skillsJSON')]
class SkillsControllerJSON extends AbstractController

{
/*
private EntityManagerInerface $entityManager; 
public function __construct(EntityManagerInterface $entityManager)
{
    $this->entityManager = $entityManager; 
}
 */

    // search & filter 
    
    #[Route('/search/{id}', name: 'search_skills_1JSON', methods: ['GET'])]
    public function searchSkills($id, Request $request,ManagerRegistry $doctrine, SkillsRepository $repo2): Response
    {
        $em = $doctrine->getManager(); 
        //$skills = $em->getRepository(Skills::class)->findAll();
        //$skills = $em->getRepository(Skills::class)->findByFreelancerId($id);
        $skills = $repo2->findByFreelancerId($id); 

        $search = $request->query->get('search');

 // if search query is set, get the search results and pass them to the view
        if ($search) {
            $skills = $repo2->findBySearchQuery($search, $id);
        }

       
        return $this->render('search/skills.html.twig', [
        
            'skills' => $skills,
    
        ]);
    }





//read
    #[Route('/', name: 'app_skills_indexJSON', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, NormalizerInterface $normalizer): Response
    {
        $skills = $entityManager
            ->getRepository(Skills::class)
            ->findAll();

        $skillsNormalises = $normalizer->normalize($skills, 'json', ['groups' => "SKILLS"]);

        $json = json_encode($skillsNormalises);
        return new Response($json);
    }
//create
    #[Route('/new', name: 'app_skills_newJSON', methods: ['GET', 'POST'])]
    public function new(Request $req,  NormalizerInterface $normalizer,ManagerRegistry $doctrine, EntityManagerInterface $entityManager): Response
    {
        $em = $doctrine->getManager(); 
        $skill = new Skills();

        $user = $entityManager->getRepository(User::class)->find($req->get('id'));
        $skill->setIdFreelancer($user);
        $skill->setName($req->get('name'));
        $skill->setLevel($req->get('level')); 
        $em->persist($skill); 
        $em->flush();

        $jsonContent = $normalizer->normalize($skill, 'json', ['groups' => "SKILLS"]);
        return new Response (json_encode($jsonContent)); 


    }







//show by skill id 
    #[Route('/{idSkills}', name: 'app_skills_showJSON', methods: ['GET'])]
    public function show($idSkills, Normalizerinterface $normalizer , SkillsRepository $repo): Response
    {
        $skill = $repo->find($idSkills);
        $skillNormalises = $normalizer->normalize($skill, 'json', ['groups' => "SKILLS"]);
        return new Response (json_encode($skillNormalises));
    }
//update
    #[Route('/{idSkills}/edit', name: 'app_skills_editJSON', methods: ['GET', 'POST'])]
    public function edit(Request $req,$idSkills,  NormalizerInterface $normalizer,ManagerRegistry $doctrine,EntityManagerInterface $entityManager): Response
    {
        $em = $doctrine->getManager(); 
        $user = $entityManager->getRepository(User::class)->find($req->get('id'));
        $skill = $em->getRepository(Skills::class)->find($idSkills);
        $skill->setIdFreelancer($user);
        $skill->setName($req->get('name'));
        $skill->setLevel($req->get('level'));; 
        
        $em->flush();

        $jsonContent = $normalizer->normalize($skill, 'json', ['groups' => "SKILLS"]);
        return new Response ( "Skill updated successfully" . json_encode($jsonContent)); 

    }
//delete
    #[Route('/delete/{idSkills}', name: 'app_skills_deleteJSON', methods: ['GET','POST'])]
    public function delete(Request $req,$idSkills,  NormalizerInterface $normalizer,ManagerRegistry $doctrine,EntityManagerInterface $entityManager): Response
    {
        $em = $doctrine->getManager(); 

        //$user = $entityManager->getRepository(User::class)->find(190);
        
        $skill = $em->getRepository(Skills::class)->find($idSkills);
        $em->remove($skill); 
        $em->flush();
        $jsonContent = $normalizer->normalize($skill, 'json', ['groups' => "SKILLS"]);
        return new Response ( "Skill deleted successfully" . json_encode($jsonContent)); 

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

    #[Route('/f/{id}', name: 'app_skills_byfreelancerJSON', methods: ['GET'])]
    //public function getfreelancer($id, EntityManagerInterface $entityManager): Response
    public function getf(Request $req,$id,SkillsRepository $repo,  NormalizerInterface $normalizer,ManagerRegistry $doctrine,EntityManagerInterface $entityManager): Response
    
    {

        //$skills = $repo->findByFreelancerId($id); 
        $s = $repo->findByFreelancerId($id); 
        $portfolioNormalises = $normalizer->normalize($s, 'json', ['groups' => "SKILLS"]);
        return new Response (json_encode($portfolioNormalises));
    }







}
