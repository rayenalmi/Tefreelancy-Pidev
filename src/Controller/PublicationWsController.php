<?php

namespace App\Controller;

use App\Entity\PublicationWs;
use App\Entity\WorkspacePost;
use App\Form\PublicationWsType;
use App\Repository\PublicationWsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/publication/ws')]
class PublicationWsController extends AbstractController
{
    #[Route('/', name: 'app_publication_ws_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $publicationWs = $entityManager
            ->getRepository(PublicationWs::class)
            ->findBy([], ['creationdate' => 'DESC']);

        return $this->render('publication_ws/index.html.twig', [
            'publication_ws' => $publicationWs,
        ]);
    }


    #[Route("/AllPostsJson/{id}", name: "posts_json")]
    public function getAllPostsJson($id, PublicationWsRepository $repo, SerializerInterface $serializer)
    {
        $posts = $repo->getPublicationWssForWorkspace($id);
        $json = $serializer->serialize($posts, 'json', ['groups' => "posts"]);
        return new Response($json);
    }


    #[Route('/new', name: 'app_publication_ws_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $publicationW = new PublicationWs();
        $form = $this->createForm(PublicationWsType::class, $publicationW);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($publicationW);
            $entityManager->flush();

            return $this->redirectToRoute('app_publication_ws_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('publication_ws/new.html.twig', [
            'publication_w' => $publicationW,
            'form' => $form,
        ]);
    }

    #[Route('/addpost/{id}', name: 'app_addpost', methods: ['GET', 'POST'])]
    public function addPost($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $publicationW = new PublicationWs();
        $form = $this->createForm(PublicationWsType::class, $publicationW);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingPost = $entityManager->getRepository(PublicationWs::class)->findOneBy(['title' => $publicationW->getTitle()]);

            if ($existingPost != null) {
                $message = 'Post already exists';
                return $this->renderForm('publication_ws/new.html.twig', [
                    'publication_w' => $publicationW,
                    'workspaceId' => $id,
                    'form' => $form,
                    'message' => $message
                ]);
            }


            $entityManager->persist($publicationW);
            $entityManager->flush();

            // Add the workspace post
            $workspacePost = new WorkspacePost();
            $workspacePost->setWorkspaceId($id);
            $workspacePost->setPublicationId($publicationW->getId());
            $entityManager->persist($workspacePost);
            $entityManager->flush();

            return $this->redirectToRoute('app_workspace_homews', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('publication_ws/new.html.twig', [
            'publication_w' => $publicationW,
            'workspaceId' => $id,
            'form' => $form,
        ]);
    }

    #[Route("/addPostJSON/new/{id}", name: "addPostJson")]
    public function addPostJSON($id, Request $req, EntityManagerInterface $entityManager,   NormalizerInterface $Normalizer)
    {

        $Post = new PublicationWs();
        $Post->setTitle($req->get('title'));
        $Post->setContent($req->get('content'));
        $Post->setAuthor($req->get('author'));
        $entityManager->persist($Post);
        $entityManager->flush();

        $workspacePost = new WorkspacePost();
        $workspacePost->setWorkspaceId($id);
        $workspacePost->setPublicationId($Post->getId());
        $entityManager->persist($workspacePost);
        $entityManager->flush();


        $jsonContent = $Normalizer->normalize($Post, 'json', ['groups' => 'posts']);
        return new Response(json_encode($jsonContent));
    }



    #[Route('/{id}/{workspaceId}', name: 'app_publication_ws_show', methods: ['GET'])]
    public function show($workspaceId, PublicationWs $publicationW): Response
    {
        return $this->render('publication_ws/show.html.twig', [
            'publication_w' => $publicationW,
            'workspaceId' => $workspaceId,
        ]);
    }

    #[Route('/{id}/edit/{workspaceId}', name: 'app_publication_ws_edit', methods: ['GET', 'POST'])]
    public function edit($workspaceId, Request $request, PublicationWs $publicationW, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PublicationWsType::class, $publicationW);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingPost = $entityManager->getRepository(PublicationWs::class)->findOneBy(['title' => $publicationW->getTitle()]);

            if ($existingPost != null) {
                $message = 'Post already exists';
                return $this->renderForm('publication_ws/new.html.twig', [
                    'publication_w' => $publicationW,
                    'workspaceId' => $workspaceId,
                    'form' => $form,
                    'message' => $message
                ]);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_workspace_homews', ['id' => $workspaceId], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('publication_ws/edit.html.twig', [
            'publication_w' => $publicationW,
            'form' => $form,
            'workspaceId' => $workspaceId,
        ]);
    }

    #[Route("/updatePostJSON/{id}/{workspaceId}", name: "updatePostJSON")]
    public function updatePostJSON($workspaceId, Request $req, $id, NormalizerInterface $Normalizer, EntityManagerInterface $entityManager)
    {

        $Post = $entityManager->getRepository(PublicationWs::class)->find($id);
        $Post->setTitle($req->get('title'));
        $Post->setContent($req->get('content'));
        $Post->setContent($req->get('author'));
        $entityManager->flush();

        $jsonContent = $Normalizer->normalize($Post, 'json', ['groups' => 'posts']);
        return new Response("Post updated successfully " . json_encode($jsonContent));
    }

    #[Route('/{id}/{workspaceId}', name: 'app_publication_ws_delete', methods: ['POST'])]
    public function delete($workspaceId, Request $request, PublicationWs $publicationW, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $publicationW->getId(), $request->request->get('_token'))) {
            $entityManager->remove($publicationW);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_workspace_homews', ['id' => $workspaceId], Response::HTTP_SEE_OTHER);
    }


    #[Route("/deletePostJSON/{id}/{workspaceId}", name: "deletePostJSON")]
    public function deletePostJSON($id, NormalizerInterface $Normalizer, EntityManagerInterface $entityManager)
    {

        $post = $entityManager->getRepository(PublicationWs::class)->find($id);
        $entityManager->remove($post);
        $entityManager->flush();
        $jsonContent = $Normalizer->normalize($post, 'json', ['groups' => 'posts']);
        return new Response("Post deleted successfully " . json_encode($jsonContent));
    }
}
