<?php

namespace App\Controller;

use App\Entity\Community;
use App\Entity\User;

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

#[Route('/community')]
class CommunityController extends AbstractController
{
    #[Route('/', name: 'app_community_index', methods: ['GET'])]
    public function index(
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $totalGroups = $entityManager
            ->getRepository(Community::class)
            ->count([]);
        $itemsPerPage = ceil($totalGroups / 4);
        $pagination = $paginator->paginate(
            $entityManager->getRepository(Community::class)->findAll(),
            $request->query->getInt('page', 1),
            $itemsPerPage
        );

        return $this->render('community/index.html.twig', [
            'pagination' => $pagination,
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
    #[Route('/search-groups', name: 'search_groups',methods: ['GET'])]
    public function searchGroupsByName(
        Request $request,
        ManagerRegistry $doctrine
    ) {
        $name = $request->query->get('name');
        $queryBuilder = $doctrine
            ->getManager()
            ->createQueryBuilder()
            ->select('g')
            ->from(Community::class, 'g')
            ->where('g.name LIKE :name')
            ->setParameter('name', '%' . $name . '%');

        $groups = $queryBuilder->getQuery()->getResult();
        $data = array_map(function ($group) {
            return [
                'id' => $group->getIdCommunity(),
                'name' => $group->getName(),
            ];
        }, $groups);

        return new JsonResponse($data);
    }

    #[Route('/sort-groups', name: 'sort_groups',methods: ['GET'])]

    public function sortGroupsByName(
        Request $request,
        ManagerRegistry $doctrine
    ) {
        $groups = $this->getDoctrine()
            ->getRepository(Community::class)
            ->findBy([], ['name' => 'ASC']);

        // Convert the groups to an array of associative arrays for easier serialization
        $data = array_map(function ($group) {
            return [
                'id' => $group->getId(),
                'name' => $group->getName(),
                // Add any other properties you want to include in the JSON response
            ];
        }, $groups);

        return new JsonResponse($data);
    }
    // public function countLikes(
    //     int $postId,
    //     EntityManagerInterface $entityManager
    // ): int {
    //     $count = $entityManager
    //         ->getRepository(Grouppostlikes::class)
    //         ->createQueryBuilder('gpl')
    //         ->select('COUNT(gpl.idgrouppost)')
    //         ->where('gpl.idgrouppost = :postId')
    //         ->setParameter('postId', $grouppostId)
    //         ->getQuery()
    //         ->getSingleScalarResult();
    //     return $count;
    // }
    #[Route('/{idCommunity}', name: 'app_community_show', methods: ['GET'])]
    public function show(
        Community $community,
        int $idCommunity,

        EntityManagerInterface $entityManager,
        GrouppostController $likesController
    ): Response {
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
            $groupPost->userData = $entityManager
                ->getRepository(User::class)
                ->findOneBy(['idUser' => $groupPost->getUser()]);

            return $groupPost;
        },
        $groupposts);
        // $chart = new LineChart();
        // $chart->getData()->setArrayToDataTable($modifiedPosts);
        // $chart->getOptions()->setTitle('Likes on Group Posts');
        // $chart
        //     ->getOptions()
        //     ->getHAxis()
        //     ->setTitle('Post Title');
        // $chart
        //     ->getOptions()
        //     ->getVAxis()
        //     ->setTitle('Number of Likes');

        return $this->render('community/show.html.twig', [
            'community' => $community,
            'groupposts' => $modifiedPosts,
            // 'chart' => $chart,
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
