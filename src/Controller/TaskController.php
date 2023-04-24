<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\WorkspaceTask;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/task')]
class TaskController extends AbstractController
{
    #[Route('/', name: 'app_task_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $tasks = $entityManager
            ->getRepository(Task::class)
            ->findBy([], ['deadline' => 'DESC']);


        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }




    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        $notifications = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    

    #[Route('/addtask/{id}', name: 'app_addtask', methods: ['GET', 'POST'])]
    public function addPost($id, Request $request, EntityManagerInterface $entityManager, TaskRepository $repo): Response
    {
        $Task = new Task();
        $form = $this->createForm(TaskType::class, $Task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $existingTask = $entityManager->getRepository(Task::class)->findOneBy(['title' => $Task->getTitle()]);

            if ($existingTask != null) {
                $message = 'Task already exists';
                return $this->renderForm('task/new.html.twig', [
                    'publication_w' => $Task,
                    'workspaceId' => $id,
                    'form' => $form,
                    'message' => $message
                ]);
            }

            $entityManager->persist($Task);
            $entityManager->flush();

            // Add the workspace task
            $workspaceTask = new WorkspaceTask();
            $workspaceTask->setWorkspaceId($id);
            $workspaceTask->setTaskId($Task->getId());
            $entityManager->persist($workspaceTask);
            $entityManager->flush();

            $taskId = $Task->getId();
            $this->addFlash('success', sprintf('%d', $taskId));
            return $this->redirectToRoute('app_workspace_homews', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task/new.html.twig', [
            'publication_w' => $Task,
            'workspaceId' => $id,
            'form' => $form
        ]);
    }




    #[Route('/notifications', name: 'app_get_notifications', methods: ['GET'])]
    public function getNotifications()
    {
        // Fetch notifications from the database
        $notifications = [
            ['id' => 1, 'message' => 'A New Task Has Been Added '],
        ];

        return new JsonResponse($notifications);
    }

    #[Route('/notification-count', name: 'app_get_notification_count', methods: ['GET'])]
    public function getNotificationCount()
    {
        // Fetch notification count from the database or another source
        $count = 5;

        return new JsonResponse([
            'count' => $count,
        ]);
    }

    #[Route('/{id}/{workspaceId}', name: 'app_task_show', methods: ['GET'])]
    public function show($workspaceId, Task $task): Response
    {
        return $this->render('task/show.html.twig', [
            'task' => $task,
            'workspaceId' => $workspaceId,
        ]);
    }

    #[Route('/{id}/edit/{workspaceId}', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit($workspaceId, Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingTask = $entityManager->getRepository(Task::class)->findOneBy(['title' => $task->getTitle()]);
            //// $existingTask = $repo->findByName($Task->getTitle());
            if ($existingTask != null) {
                $message = 'Task already exists';
                return $this->renderForm('task/new.html.twig', [
                    'publication_w' => $task,
                    'workspaceId' => $workspaceId,
                    'form' => $form,
                    'message' => $message
                ]);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_workspace_homews',  ['id' => $workspaceId], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
            'workspaceId' => $workspaceId,
        ]);
    }

    #[Route('/{id}/setTrue/{workspaceId}', name: 'app_task_setTrue', methods: ['GET', 'POST'])]
    public function setTrue($workspaceId, Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        $task->setCompleted(true); // set the completed property to true
        $entityManager->flush(); // persist the changes to the database

        return $this->redirectToRoute('app_workspace_homews',  ['id' => $workspaceId], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id}/{workspaceId}', name: 'app_task_delete', methods: ['POST'])]
    public function delete($workspaceId, Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $task->getId(), $request->request->get('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_workspace_homews', ['id' => $workspaceId], Response::HTTP_SEE_OTHER);
    }
}