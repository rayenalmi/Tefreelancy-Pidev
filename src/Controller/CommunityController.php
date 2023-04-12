<?php

namespace App\Controller;

use App\Entity\Community;
use App\Entity\Grouppost;

use App\Form\CommunityType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/community')]
class CommunityController extends AbstractController
{
    #[Route('/', name: 'app_community_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $communities = $entityManager
            ->getRepository(Community::class)
            ->findAll();

        return $this->render('community/index.html.twig', [
            'communities' => $communities,
        ]);
    }

    #[Route('/new', name: 'app_community_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $community = new Community();
        $form = $this->createForm(CommunityType::class, $community);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($community);
            $entityManager->flush();

            return $this->redirectToRoute(
                'app_community_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('community/new.html.twig', [
            'community' => $community,
            'form' => $form,
        ]);
    }

    #[Route('/{idCommunity}', name: 'app_community_show', methods: ['GET'])]
    public function show(
        Community $community,
        int $idCommunity,
        EntityManagerInterface $entityManager
    ): Response {
        $groupposts = $entityManager
            ->getRepository(Grouppost::class)
            ->findBy(['idCommunity' => $idCommunity]);
        return $this->render('community/show.html.twig', [
            'community' => $community,
            'groupposts' => $groupposts,
        ]);
    }

    #[Route('/{idCommunity}/edit', name: 'app_community_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Community $community,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(CommunityType::class, $community);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute(
                'app_community_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('community/edit.html.twig', [
            'community' => $community,
            'form' => $form,
        ]);
    }

    #[Route('/{idCommunity}/delete', name: 'app_community_delete', methods: ['GET'])]
    public function delete(
        Request $request,
        int $idCommunity,
        EntityManagerInterface $entityManager
    ): Response {
        $community = $entityManager
            ->getRepository(Community::class)
            ->find($idCommunity);
        $entityManager->remove($community);
        $entityManager->flush();
        return $this->redirectToRoute(
            'app_community_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
