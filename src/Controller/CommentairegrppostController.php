<?php

namespace App\Controller;

use App\Entity\Commentairegrppost;
use App\Form\CommentairegrppostType;
use App\Repository\CommentairegrppostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commentairegrppost')]
class CommentairegrppostController extends AbstractController
{
    #[Route('/{idgrppost}', name: 'app_commentairegrppost_index', methods: ['GET'])]
    public function index(
        CommentairegrppostRepository $commentairegrppostRepository,
        int $idgrppost
    ): Response {
        return $this->render('commentairegrppost/index.html.twig', [
            'commentairegrpposts' => $commentairegrppostRepository->findBy([
                'Idgrppost' => $idgrppost,
            ]),
            'Idgrppost' => $idgrppost,
        ]);
    }

    #[Route('/new/{idgrppost}', name: 'app_commentairegrppost_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        CommentairegrppostRepository $commentairegrppostRepository,
        int $idgrppost
    ): Response {
        $commentairegrppost = new Commentairegrppost();
        $session = $request->getSession();
        $user = $session->get('user');
        $commentairegrppost->setUser($user->getIdUser());
        $commentairegrppost->setIdgrppost($idgrppost);

        $form = $this->createForm(
            CommentairegrppostType::class,
            $commentairegrppost
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentairegrppostRepository->add($commentairegrppost, true);

            return $this->redirectToRoute(
                'app_commentairegrppost_index',
                ['idgrppost' => $idgrppost],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('commentairegrppost/new.html.twig', [
            'commentairegrppost' => $commentairegrppost,
            'form' => $form,
            'Idgrppost' => $idgrppost,
        ]);
    }

    #[Route('/{id}', name: 'app_commentairegrppost_show', methods: ['GET'])]
    public function show(Commentairegrppost $commentairegrppost): Response
    {
        return $this->render('commentairegrppost/show.html.twig', [
            'commentairegrppost' => $commentairegrppost,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commentairegrppost_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Commentairegrppost $commentairegrppost,
        CommentairegrppostRepository $commentairegrppostRepository
    ): Response {
        $form = $this->createForm(
            CommentairegrppostType::class,
            $commentairegrppost
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentairegrppostRepository->add($commentairegrppost, true);

            return $this->redirectToRoute(
                'app_commentairegrppost_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('commentairegrppost/edit.html.twig', [
            'commentairegrppost' => $commentairegrppost,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commentairegrppost_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Commentairegrppost $commentairegrppost,
        CommentairegrppostRepository $commentairegrppostRepository
    ): Response {
        if (
            $this->isCsrfTokenValid(
                'delete' . $commentairegrppost->getId(),
                $request->request->get('_token')
            )
        ) {
            $commentairegrppostRepository->remove($commentairegrppost, true);
        }

        return $this->redirectToRoute(
            'app_commentairegrppost_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
