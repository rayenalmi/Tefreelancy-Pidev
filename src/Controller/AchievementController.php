<?php

namespace App\Controller;

use App\Entity\Achievement;
use App\Form\AchievementType;
use App\Repository\AchievementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/achievement')]
class AchievementController extends AbstractController
{


    #[Route('/f', name: 'app_achievement_byfreelancer', methods: ['GET'])]
    //public function getfreelancer($id, EntityManagerInterface $entityManager): Response
    public function getf(Request $request,  AchievementRepository $repo): Response
    
    {


        $session = $request->getSession();
        $u =  $session->get('user'); 
        //$experiences = $repo->findByFreelancerId($u->getIdUser());  

        $achievements = $repo->findByFreelancerId($u->getIdUser()); 
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

            return $this->redirectToRoute('app_achievement_byfreelancer', [], Response::HTTP_SEE_OTHER);
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

    #[Route('/{idAchivement}/edit', name: 'app_achievement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Achievement $achievement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AchievementType::class, $achievement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_achievement_byfreelancer', [], Response::HTTP_SEE_OTHER);
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

        return $this->redirectToRoute('app_achievement_byfreelancer', [], Response::HTTP_SEE_OTHER);
    }


    



    


}
