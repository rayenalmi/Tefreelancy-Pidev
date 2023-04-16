<?php

namespace App\Controller;

use App\Entity\Grouppostlikes;
use App\Form\GrouppostlikesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/grouppostlikes')]
class GrouppostlikesController extends AbstractController
{
    #[Route('/', name: 'app_grouppostlikes_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $grouppostlikes = $entityManager
            ->getRepository(Grouppostlikes::class)
            ->findAll();

        return $this->render('grouppostlikes/index.html.twig', [
            'grouppostlikes' => $grouppostlikes,
        ]);
    }

    #[Route('/new', name: 'app_grouppostlikes_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $grouppostlike = new Grouppostlikes();
    //     $form = $this->createForm(GrouppostlikesType::class, $grouppostlike);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->persist($grouppostlike);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_grouppostlikes_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('grouppostlikes/new.html.twig', [
    //         'grouppostlike' => $grouppostlike,
    //         'form' => $form,
    //     ]);
    // }
    // Controller method that handles the creation of new likes
    public function likePost(Request $request, Post $post)
    {
        $like = new Like();
        $like->setPost($post);
        $like->setUser(1);
        $form = $this->createForm(GrouppostlikesType::class, $like);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($like);
            $entityManager->flush();

            return $this->redirectToRoute('app_grouppostlikes_index', [
                'id' => $post->getId(),
            ]);
        }

        // Render the view of the post with the form to add a new like
        return $this->render('grouppostlikes/new.html.twig', [
            'post' => $post,
            'likesCount' => $post->getLikes()->count(),
            'form' => $form->createView(),
        ]);
    }
    // Controller method that renders the view of the post
    public function countLikes(Post $post)
    {
        // Retrieve the total number of likes for the current post
        $likesCount = $this->getDoctrine()
            ->getRepository(Grouppostlikes::class)
            ->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.post = :post')
            ->setParameter('post', $post)
            ->getQuery()
            ->getSingleScalarResult();

        // Render the view of the post with the likes count
        return $this->render('communitypost/show.html.twig', [
            'post' => $post,
            'likesCount' => $likesCount,
        ]);
    }

    #[Route('/{idgrouppost}/edit', name: 'app_grouppostlikes_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Grouppostlikes $grouppostlike,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(GrouppostlikesType::class, $grouppostlike);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute(
                'app_grouppostlikes_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('grouppostlikes/edit.html.twig', [
            'grouppostlike' => $grouppostlike,
            'form' => $form,
        ]);
    }

    #[Route('/{idgrouppost}', name: 'app_grouppostlikes_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Grouppostlikes $grouppostlike,
        EntityManagerInterface $entityManager
    ): Response {
        if (
            $this->isCsrfTokenValid(
                'delete' . $grouppostlike->getIdgrouppost(),
                $request->request->get('_token')
            )
        ) {
            $entityManager->remove($grouppostlike);
            $entityManager->flush();
        }

        return $this->redirectToRoute(
            'app_grouppostlikes_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
