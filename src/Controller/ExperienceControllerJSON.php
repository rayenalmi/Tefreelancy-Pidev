<?php

namespace App\Controller;
use App\Entity\User; 
use App\Entity\Experience;
use App\Form\ExperienceType;
use App\Repository\ExperienceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Doctrine\Persistence\ManagerRegistry;


#[Route('/experienceJSON')]
class ExperienceControllerJSON extends AbstractController
{
    //read
    #[Route('/', name: 'app_experience_indexJSON', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, NormalizerInterface $normalizer): Response
    {
        $experiences = $entityManager
        
            ->getRepository(Experience::class)
            ->findAll();

        $experiencesNormalises = $normalizer->normalize($experiences, 'json', ['groups' => "EXP"]);

        $json = json_encode($experiencesNormalises);
        return new Response($json);
    }
//create
    #[Route('/new', name: 'app_experience_newJSON', methods: ['GET', 'POST'])]
    public function new(Request $req,  NormalizerInterface $normalizer,ManagerRegistry $doctrine, EntityManagerInterface $entityManager): Response
    {
        $experience = new Experience();
        $em = $doctrine->getManager(); 
        

        $user = $entityManager->getRepository(User::class)->find(190);
        $experience->setIdFreelancer(190);
        $experience->setTitle($req->get('title'));
        $experience->setDescription($req->get('description')); 
        $experience->setLocation($req->get('location')); 
        $experience->setDuration($req->get('duration')); 
        $experience->setType($req->get('type')); 


        $em->persist($experience); 
        $em->flush();

        $jsonContent = $normalizer->normalize($experience, 'json', ['groups' => "EXP"]);
        return new Response (json_encode($jsonContent)); 
 


    }
//show by exp id 
    #[Route('/{idExperience}', name: 'app_experience_showJSON', methods: ['GET'])]
    public function show($idExperience, Normalizerinterface $normalizer , ExperienceRepository $repo): Response
    {
        $exp = $repo->find($idExperience);
    
        $expNormalises = $normalizer->normalize($exp, 'json', ['groups' => "EXP"]);
        return new Response (json_encode($expNormalises));
    }

    //update
    #[Route('/{idExperience}/edit', name: 'app_experience_editJSON', methods: ['GET', 'POST'])]
    public function edit(Request $req,$idExperience,  NormalizerInterface $normalizer,ManagerRegistry $doctrine,EntityManagerInterface $entityManager): Response
    {
        $em = $doctrine->getManager(); 
        $user = $entityManager->getRepository(User::class)->find(190);
        $exp = $em->getRepository(Experience::class)->find($idExperience);
        $exp->setIdFreelancer(190);
        $exp->setTitle($req->get('title'));
        $exp->setDescription($req->get('description')); 
        $exp->setLocation($req->get('location')); 
        $exp->setDuration($req->get('duration')); 
        $exp->setType($req->get('type')); 
        
        $em->flush();

        $jsonContent = $normalizer->normalize($exp, 'json', ['groups' => "EXP"]);
        return new Response ( "Exp updated successfully" . json_encode($jsonContent));  
    }
//delete
    #[Route('/delete/{idExperience}', name: 'app_experience_deleteJSON', methods: ['GET','POST'])]
    public function delete(Request $req,$idExperience,  NormalizerInterface $normalizer,ManagerRegistry $doctrine,EntityManagerInterface $entityManager): Response
    {
        $em = $doctrine->getManager(); 

        //$user = $entityManager->getRepository(User::class)->find(190);
        
        $exp = $em->getRepository(Experience::class)->find($idExperience);
        $em->remove($exp); 
        $em->flush();
        $jsonContent = $normalizer->normalize($exp, 'json', ['groups' => "EXP"]);
        return new Response ( "Exp deleted successfully" . json_encode($jsonContent)); 

    }


    #[Route('/f/{id}', name: 'app_experience_byfreelancerJSON', methods: ['GET'])]
    //public function getfreelancer($id, EntityManagerInterface $entityManager): Response
    public function getf(Request $req,$id,ExperienceRepository $repo,  NormalizerInterface $normalizer,ManagerRegistry $doctrine,EntityManagerInterface $entityManager): Response
    
    {
        $e = $repo->findByFreelancerId($id); 
        $expNormalises = $normalizer->normalize($e, 'json', ['groups' => "EXP"]);
        return new Response (json_encode($expNormalises));
    }



}
