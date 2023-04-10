<?php

namespace App\Controller;

use App\Entity\Achievement;
use App\Form\AchievementType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/achievement')]
class AchievementController extends AbstractController
{
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
}
