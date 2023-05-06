<?php

namespace App\Controller;

use App\Entity\Community;
use App\Entity\Grouppostlikes;
use App\Entity\Grouppost;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\CommunityType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\DoctrineBundle\Controller\ControllerTrait;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\LineChart;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/json/community')]
class CommunityJson extends AbstractController
{
    #[Route('/', name: 'app_communityjson_index', methods: ['GET'])]
    public function index(
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator,
        Request $request,
        SerializerInterface $serializer
    ): Response {
        $totalGroups = $entityManager
            ->getRepository(Community::class)
            ->count([]);
        $itemsPerPage = ceil($totalGroups / 4);
        $pagination = $paginator->paginate(
            $entityManager->getRepository(Community::class)->findAll(),
            $request->query->getInt('page', 1),
            $totalGroups
        );

        $json = $serializer->serialize($pagination, 'json', [
            'groups' => 'pagination',
        ]);

        return new Response($json);
    }

    #[Route('/new', name: 'app_communityjson_new', methods: ['GET', 'POST'])]
    public function new(
        Request $req,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        NormalizerInterface $Normalizer
    ) {
        $community = new Community();
        $community->setIdCommunity($req->get('id'));
        $community->setName($req->get('name'));
        $community->setDescription($req->get('desc'));
        $entityManager->persist($community);
        $entityManager->flush();
        $jsonContent = $Normalizer->normalize($community, 'json', [
            'groups' => 'community',
        ]);
        return new Response(json_encode($jsonContent));
    }

    #[Route('/{idCommunity}', name: 'app_community_show', methods: ['GET'])]
    public function show(
        Community $community,
        int $idCommunity,
        EntityManagerInterface $entityManager,
        GrouppostController $likesController,
        NormalizerInterface $Normalizer
    ): JsonResponse {
        $groupposts = $entityManager
            ->getRepository(Grouppost::class)
            ->findBy(['idCommunity' => $idCommunity]);
        $modifiedPosts = array_map(function ($groupPost) use (
            $likesController,
            $entityManager
        ) {
            $groupPost->numLikes = $likesController->countLikes(
                $groupPost->getIdGrouppost(),
                $entityManager
            );
            return $groupPost;
        },
        $groupposts);

        $data = $Normalizer->normalize(
            [
                'community' => $community,
                'groupposts' => $modifiedPosts,
            ],
            'json',
            [
                'community' => $community,
                'groupposts' => $modifiedPosts,
            ]
        );
        // $data = [
        //     'community' => $community,
        //     'groupposts' => $modifiedPosts,
        // ];
        $jsonData = json_encode($data);
        return new JsonResponse($jsonData, 200, [], true);
    }

    #[Route('/{idCommunity}/edit', name: 'app_communityjson_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $req,
        Community $community,
        int $idCommunity,
        EntityManagerInterface $em,
        NormalizerInterface $Normalizer
    ): Response {
        $community = $em->getRepository(Community::class)->find($idCommunity);
        $community->setName($req->get('name'));
        $community->setDescription($req->get('desc'));
        $em->flush();
        $jsonContent = $Normalizer->normalize($community, 'json', [
            'groups' => 'community',
        ]);
        return new Response(
            'Community updated successfully ' . json_encode($jsonContent)
        );
    }

    #[Route('/{idCommunity}/delete', name: 'app_communityjson_delete', methods: ['GET'])]
    public function delete(
        Request $request,
        int $idCommunity,
        EntityManagerInterface $entityManager,
        NormalizerInterface $Normalizer
    ): Response {
        $community = $entityManager
            ->getRepository(Community::class)
            ->find($idCommunity);
        $entityManager->remove($community);
        $entityManager->flush();

        $jsonContent = $Normalizer->normalize($community, 'json', [
            'groups' => 'community',
        ]);
        return new Response(
            'Community deleted successfully ' . json_encode($jsonContent)
        );
    }
}
