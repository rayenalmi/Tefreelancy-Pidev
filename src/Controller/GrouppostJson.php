<?php

namespace App\Controller;

use App\Entity\Grouppost;
use App\Entity\Grouppostlikes;

use App\Form\GrouppostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/json/grouppost')]
class GrouppostJson extends AbstractController
{
    #[Route('/new/{idCommunity}', name: 'app_grouppostJSON_new', methods: ['GET', 'POST'])]
    public function new(
        Request $req,
        EntityManagerInterface $entityManager,
        int $idCommunity,
        SerializerInterface $serializer,
        NormalizerInterface $Normalizer
    ): Response {
        $grouppost = new Grouppost();
        $grouppost->setUser(1);
        $grouppost->setIdCommunity($idCommunity);
        $grouppost->setContext($req->get('context'));
        $entityManager->persist($grouppost);
        $entityManager->flush();
        $jsonContent = $Normalizer->normalize($grouppost, 'json', [
            'groups' => 'grouppost',
        ]);
        return new Response(json_encode($jsonContent));
    }

    #[Route('/{idGrouppost}/edit', name: 'app_grouppostJSON_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $req,
        Grouppost $grouppost,
        EntityManagerInterface $entityManager,
        int $idGrouppost,
        NormalizerInterface $Normalizer
    ): Response {
        $post = $entityManager
            ->getRepository(Grouppost::class)
            ->find($idGrouppost);
        $post->setContext($req->get('context'));
        $entityManager->flush();
        $jsonContent = $Normalizer->normalize($post, 'json', [
            'groups' => 'post',
        ]);
        return new Response(
            'Posts updated successfully ' . json_encode($jsonContent)
        );
    }

    #[Route('/{idGrouppost}/delete', name: 'app_grouppostJSON_delete', methods: ['GET'])]
    public function delete(
        Request $request,
        int $idGrouppost,
        EntityManagerInterface $entityManager,
        NormalizerInterface $Normalizer
    ): Response {
        $grouppost = $entityManager
            ->getRepository(Grouppost::class)
            ->find($idGrouppost);
        $entityManager->remove($grouppost);
        $entityManager->flush();
        $jsonContent = $Normalizer->normalize($grouppost, 'json', [
            'groups' => 'grouppost',
        ]);
        return new Response(
            'pOST deleted successfully ' . json_encode($jsonContent)
        );
    }
}
