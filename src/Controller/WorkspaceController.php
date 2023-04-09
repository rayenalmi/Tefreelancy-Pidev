<?php

namespace App\Controller;

use App\Entity\PublicationWs;
use App\Entity\Task;
use App\Entity\Workspace;
use App\Form\WorkspaceType;
use App\Repository\PublicationWsRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/workspace')]
class WorkspaceController extends AbstractController
{
    private $workspaceId;


    #[Route('/', name: 'app_workspace_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $workspaces = $entityManager
            ->getRepository(Workspace::class)
            ->findBy([], ['id' => 'DESC']);

        return $this->render('workspace/index.html.twig', [
            'workspaces' => $workspaces,
        ]);
    }



    #[Route('/homews/{id}', name: 'app_workspace_homews', methods: ['GET'])]
    public function homeWs($id,EntityManagerInterface $entityManager,TaskRepository $repo,PublicationWsRepository $repo2): Response
    {
        $workspaces = $entityManager
            ->getRepository(Workspace::class)
            ->findAll();

        $publicationWs = $repo2->getPublicationWssForWorkspace($id);

        $tasks = $repo->getTasksForWorkspace($id);
        return $this->render('home_ws/index.html.twig', [
            'workspaces' => $workspaces,
            'publication_ws' => $publicationWs,
            'workspaceId' => $id,
            'tasks' => $tasks
        ]);
    }


    #[Route('/editws/{id}', name: 'app_editworkspace', methods: ['GET'])]
    public function editWorkSpace($id,EntityManagerInterface $entityManager,TaskRepository $repo,PublicationWsRepository $repo2): Response
    {
        $workspaces = $entityManager
            ->getRepository(Workspace::class)
            ->findAll();

        $publicationWs = $repo2->getPublicationWssForWorkspace($id);

        $tasks = $repo->getTasksForWorkspace($id);

        return $this->render('home_ws/edit.html.twig', [
            'workspaces' => $workspaces,
            'publication_ws' => $publicationWs,
            'workspaceId' => $id,
            'tasks' => $tasks
        ]);
    }

    #[Route('/new', name: 'app_workspace_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $workspace = new Workspace();
        $form = $this->createForm(WorkspaceType::class, $workspace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($workspace);
            $entityManager->flush();

            return $this->redirectToRoute('app_workspace_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('workspace/new.html.twig', [
            'workspace' => $workspace,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_workspace_show', methods: ['GET'])]
    public function show(Workspace $workspace): Response
    {
        return $this->render('workspace/show.html.twig', [
            'workspace' => $workspace
        ]);
    }

    #[Route('/{id}/edit', name: 'app_workspace_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Workspace $workspace, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WorkspaceType::class, $workspace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_workspace_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('workspace/edit.html.twig', [
            'workspace' => $workspace,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_workspace_delete', methods: ['POST'])]
    public function delete(Request $request, Workspace $workspace, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $workspace->getId(), $request->request->get('_token'))) {
            $entityManager->remove($workspace);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_workspace_index', [], Response::HTTP_SEE_OTHER);
    }
}
