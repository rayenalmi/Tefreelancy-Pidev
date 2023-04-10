<?php

namespace App\Controller;

use App\Entity\PublicationWs;
use App\Entity\Task;
use App\Entity\User;
use App\Entity\Workspace;
use App\Entity\WorkspaceFreelancer;
use App\Form\WorkspaceType;
use App\Repository\PublicationWsRepository;
use App\Repository\TaskRepository;
use App\Repository\WorkspaceRepository;
use App\Form\AddFreelancerWsType;

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
    public function index(EntityManagerInterface $entityManager,WorkspaceRepository $repo3): Response
    {
        /* $workspaces = $entityManager
            ->getRepository(Workspace::class)
            ->findBy([], ['id' => 'DESC']); */
        $workspaces=$repo3->getWorkspacesForFreelancer(12);
        return $this->render('workspace/index.html.twig', [
            'workspaces' => $workspaces,
        ]);
    }



    #[Route('/homews/{id}', name: 'app_workspace_homews', methods: ['GET'])]
    public function homeWs($id,EntityManagerInterface $entityManager,TaskRepository $repo,PublicationWsRepository $repo2,WorkspaceRepository $repo3): Response
    {
        $workspaces = $entityManager
            ->getRepository(Workspace::class)
            ->findAll();
        $freelancers =$repo3->getFreelancersForWorkspace($id);

        $publicationWs = $repo2->getPublicationWssForWorkspace($id);

        $tasks = $repo->getTasksForWorkspace($id);
        return $this->render('home_ws/index.html.twig', [
            'workspaces' => $workspaces,
            'publication_ws' => $publicationWs,
            'workspaceId' => $id,
            'tasks' => $tasks,
            'freelancers'=>$freelancers
        ]);
    }


    #[Route('/editws/{id}', name: 'app_editworkspace', methods: ['GET','POST'])]
    public function editWorkSpace(Request $request,$id,EntityManagerInterface $entityManager,TaskRepository $repo,PublicationWsRepository $repo2,WorkspaceRepository $repo3): Response
    {
        $workspaces = $entityManager
            ->getRepository(Workspace::class)
            ->findAll();
        $freelancers =$repo3->getFreelancersForWorkspace($id);

        $publicationWs = $repo2->getPublicationWssForWorkspace($id);

        $tasks = $repo->getTasksForWorkspace($id);
        
        $form2 = $this->createForm(AddFreelancerWsType::class);
        $form2->handleRequest($request);
        $newFreelancer = new User();
        if ($form2->isSubmitted() && $form2->isValid()) {
            $formData = $form2->getData();
            $email = $formData['email'];
            
            $newFreelancer=$repo3->getFreelancerByEmail($email);
            $workspaceFreelancer = new WorkspaceFreelancer();
            $workspaceFreelancer->setWorkspaceId($id);
            $workspaceFreelancer->setFreelancerId($newFreelancer->getIdUser());
            $entityManager->persist($workspaceFreelancer);
            $entityManager->flush();
        }
        array_push($freelancers, $newFreelancer);
        return $this->render('home_ws/edit.html.twig', [
            'workspaces' => $workspaces,
            'publication_ws' => $publicationWs,
            'workspaceId' => $id,
            'tasks' => $tasks,
            'freelancers'=>$freelancers,
            'form2'=>$form2->createView()
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
            'form' => $form
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
