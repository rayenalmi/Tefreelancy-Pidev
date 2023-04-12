<?php

namespace App\Controller;

use App\Entity\Grouppost;
use App\Form\GrouppostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/grouppost')]
class GrouppostController extends AbstractController
{
    #[Route('/', name: 'app_grouppost_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $groupposts = $entityManager
            ->getRepository(Grouppost::class)
            ->findAll();

        return $this->render('grouppost/index.html.twig', [
            'groupposts' => $groupposts,
        ]);
    }

    #[Route('/new/{idCommunity}', name: 'app_grouppost_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        int $idCommunity
    ): Response {
        $grouppost = new Grouppost();
        $grouppost->setUser(1);
        $grouppost->setIdCommunity($idCommunity);

        $form = $this->createForm(GrouppostType::class, $grouppost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($grouppost);
            $entityManager->flush();

            return $this->redirectToRoute(
                'app_community_show',
                ['idCommunity' => $grouppost->getIdCommunity()],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('grouppost/new.html.twig', [
            'grouppost' => $grouppost,
            'form' => $form,
        ]);
    }

    #[Route('/{idGrouppost}', name: 'app_grouppost_show', methods: ['GET'])]
    public function show(
        Grouppost $grouppost,
    ): Response {
        
        return $this->render('community/show.html.twig', [
            'groupposts' => $grouppost,
        ]);
    }

    #[Route('/{idGrouppost}/edit', name: 'app_grouppost_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Grouppost $grouppost,
        EntityManagerInterface $entityManager,
        int $idGrouppost,
    ): Response {
        $form = $this->createForm(GrouppostType::class, $grouppost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute(
                'app_community_show',
                ['idCommunity' => $grouppost->getIdCommunity()],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('grouppost/edit.html.twig', [
            'grouppost' => $grouppost,
            'form' => $form,
        ]);
    }

    #[Route('/{idGrouppost}/delete', name: 'app_grouppost_delete', methods: ['GET'])]
    public function delete(
        Request $request,
        int $idGrouppost,
        EntityManagerInterface $entityManager
    ): Response {
        $grouppost = $entityManager
        ->getRepository(Grouppost::class)
        ->find($idGrouppost);
            $entityManager->remove($grouppost);
            $entityManager->flush();
        return $this->redirectToRoute(
            'app_community_show',
            ['idCommunity' => $grouppost->getIdCommunity()],
            Response::HTTP_SEE_OTHER
        );
    }
}
