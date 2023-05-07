<?php

namespace App\Controller;
use App\Entity\User; 
use App\Entity\Achievement;
use App\Form\AchievementType;
use App\Repository\AchievementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/achievement')]



class AchievementController extends AbstractController
{

    //create
#[Route('/jsonnew', name: 'app_achievement_newJSON', methods: ['GET', 'POST'])]
public function newjson(Request $req,  NormalizerInterface $normalizer,ManagerRegistry $doctrine, EntityManagerInterface $entityManager): Response
{
    $achievement = new Achievement();
    //$achievement -> setIdFreelancer(191); 
    //$achievement -> setIdOffer(12); 
    $em = $doctrine->getManager(); 
    $a = new Achievement();

    //$user = $entityManager->getRepository(User::class)->find(190);
    $a->setIdFreelancer(190);
    $a->setIdOffer($req->get('idOffer'));
    $a->setRate($req->get('rate')); 
    $em->persist($a); 
    $em->flush();

    $jsonContent = $normalizer->normalize($a, 'json', ['groups' => "ACH"]);
    return new Response (json_encode($jsonContent)); 
}


//update
#[Route('/editjson/{idAchivement}', name: 'app_achievement_editJSON', methods: ['GET', 'POST'])]
public function editjson(Request $req,$idAchivement,  NormalizerInterface $normalizer,ManagerRegistry $doctrine,EntityManagerInterface $entityManager): Response
{
    $em = $doctrine->getManager(); 
    $user = $entityManager->getRepository(User::class)->find(190);
    $a = $em->getRepository(Achievement::class)->find($idAchivement);
    $a->setIdFreelancer($user);
    $a->setIdOffer($req->get('idOffer'));
    $a->setRate($req->get('rate'));
    $em->flush();
    $jsonContent = $normalizer->normalize($a, 'json', ['groups' => "ACH"]);
    return new Response ( "achievement updated successfully" . json_encode($jsonContent)); 
}


/////////////////////////////////
//get by freelancer id 
#[Route('/json/f/{id}', name: 'app_achievement_byfreelancerJSON', methods: ['GET'])]
//public function getfreelancer($id, EntityManagerInterface $entityManager): Response
public function getfjson(Request $req,$id,AchievementRepository $repo,  NormalizerInterface $normalizer,ManagerRegistry $doctrine,EntityManagerInterface $entityManager): Response

{
    $s = $repo->findByFreelancerId($id); 
    $portfolioNormalises = $normalizer->normalize($s, 'json', ['groups' => "ACH"]);
    return new Response (json_encode($portfolioNormalises));
}






    #[Route('/', name: 'app_achievement_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $achievements = $entityManager
            ->getRepository(Achievement::class)
            ->findAll();

        return $this->render('achievement/index.html.twig', [
            'achievements' => $achievements,
        ]);
    }


    #[Route('/jsongetall', name: 'app_achievement_indexJSON')]
    public function getallach(EntityManagerInterface $entityManager, NormalizerInterface $normalizer,SerializerInterface $serializer)
    {
        $achievements = $entityManager
            ->getRepository(Achievement::class)
            ->findAll();
        /*$skillsNormalises = $normalizer->normalize($achievements, 'json', ['groups' => "ACH"]);
        $json = json_encode($skillsNormalises);
        return new Response($json);*/
        $json = $serializer->serialize($achievements, 'json', ['groups' => "ACH"]);
        return new Response($json);
    }










    #[Route('/new', name: 'app_achievement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $achievement = new Achievement();
        //$achievement -> setIdFreelancer(191); 
        //$achievement -> setIdOffer(12); 
        $form = $this->createForm(AchievementType::class, $achievement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($achievement);
            $entityManager->flush();

            return $this->redirectToRoute('app_achievement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('achievement/new.html.twig', [
            'achievement' => $achievement,
            'form' => $form,
        ]);
    }






    #[Route('/{idAchivement}', name: 'app_achievement_show', methods: ['GET'])]
    public function show(Achievement $achievement): Response
    {
        return $this->render('achievement/show.html.twig', [
            'achievement' => $achievement,
        ]);
    }




    //get by id achievement
    #[Route('/json/{idAchivement}', name: 'app_achievement_showJSON', methods: ['GET'])]
    public function showjson($idAchivement, Normalizerinterface $normalizer , AchievementRepository $repo): Response
    {
        $a = $repo->find($idAchivement);
        $achNormalises = $normalizer->normalize($a, 'json', ['groups' => "ACH"]);
        return new Response (json_encode($achNormalises));
    }












    #[Route('/{idAchivement}/edit', name: 'app_achievement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Achievement $achievement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AchievementType::class, $achievement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_achievement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('achievement/edit.html.twig', [
            'achievement' => $achievement,
            'form' => $form,
        ]);
    }

    #[Route('/{idAchivement}', name: 'app_achievement_delete', methods: ['POST'])]
    public function delete(Request $request, Achievement $achievement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$achievement->getIdAchivement(), $request->request->get('_token'))) {
            $entityManager->remove($achievement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_achievement_index', [], Response::HTTP_SEE_OTHER);
    }


    



    #[Route('/f/{id}', name: 'app_achievement_byfreelancer', methods: ['GET'])]
    //public function getfreelancer($id, EntityManagerInterface $entityManager): Response
    public function getf($id, AchievementRepository $repo): Response
    
    {
        $achievements = $repo->findByFreelancerId($id); 
        $totalrate = 0; 
        $numberofratings = 0;
            foreach ( $achievements as $i) {
                $totalrate += $i->getRate();
                $numberofratings += 1; 
            }   
            $finalrating = $totalrate / $numberofratings; 

            
        //$budget = $repo->findByOfferId($id);     
        // $totalbudget = 0; 
        // $numberofmoney = 0;
        //     foreach ( $budget as $b) {
        //         $totalbudget += $b->getSalary();
        //         $numberofmoney += 1; 
        //     }   
        //     $finalbudget = $totalbudget; 



        return $this->render('achievement/indexid.html.twig', [
            'achievements' => $achievements,
            'final_rating' => $finalrating,
           
        ]);
    }


}
