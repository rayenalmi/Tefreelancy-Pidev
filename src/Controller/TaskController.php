<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\WorkspaceTask;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

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
    #[Route("/AllTasksJson/{id}", name: "tasks_json")]
    public function getAllTasksJson($id, TaskRepository $repo, SerializerInterface $serializer)
    {
        $tasks = $repo->getTasksForWorkspace($id);
        $json = $serializer->serialize($tasks, 'json', ['groups' => "tasks"]);
        return new Response($json);
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

    #[Route("/addTaskJSON/new/{id}", name: "addTaskJson")]
    public function addTaskJSON($id, Request $req, EntityManagerInterface $entityManager,   NormalizerInterface $Normalizer)
    {
        // Get the deadline string from the URL parameters
        $deadlineStr = $_GET['deadline'];
        // Convert the deadline string to a DateTime object
        $deadline = DateTime::createFromFormat('Y-m-d', $deadlineStr);
        // Check if the conversion was successful
        if (!$deadline) {
            die('Invalid deadline format');
        }
        // Ensure that the deadline is a DateTimeInterface object
        $deadlineInterface = $deadline->getTimestamp() ? $deadline : DateTimeImmutable::createFromMutable($deadline);
        $Task = new Task();
        $Task->setTitle($req->get('title'));
        $Task->setDescription($req->get('description'));
        $Task->setDeadline($deadlineInterface);
        //$Task->setCompleted($req->get('completed'));

        $entityManager->persist($Task);
        $entityManager->flush();

        // Add the workspace task
        $workspaceTask = new WorkspaceTask();
        $workspaceTask->setWorkspaceId($id);
        $workspaceTask->setTaskId($Task->getId());
        $entityManager->persist($workspaceTask);
        $entityManager->flush();


        $jsonContent = $Normalizer->normalize($Task, 'json', ['groups' => 'tasks']);
        return new Response(json_encode($jsonContent));
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

    #[Route("/{id}", name: "task_json")]
    public function showTaskJson($id, NormalizerInterface $normalizer, TaskRepository $repo)
    {
        $task = $repo->find($id);
        $taskNormalises = $normalizer->normalize($task, 'json', ['groups' => "tasks"]);
        return new Response(json_encode($taskNormalises));
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

    #[Route("/updateTaskJSON/{id}/{workspaceId}", name: "updateTaskJSON")]
    public function updateTaskJSON($workspaceId, Request $req, $id, NormalizerInterface $Normalizer, EntityManagerInterface $entityManager)
    {
        // Get the deadline string from the URL parameters
        $deadlineStr = $_GET['deadline'];
        // Convert the deadline string to a DateTime object
        $deadline = DateTime::createFromFormat('Y-m-d', $deadlineStr);
        // Check if the conversion was successful
        if (!$deadline) {
            die('Invalid deadline format');
        }
        // Ensure that the deadline is a DateTimeInterface object
        $deadlineInterface = $deadline->getTimestamp() ? $deadline : DateTimeImmutable::createFromMutable($deadline);

        $task = $entityManager->getRepository(Task::class)->find($id);
        $task->setTitle($req->get('title'));
        $task->setDescription($req->get('description'));
        $task->setDeadline($deadlineInterface);
        $task->setCompleted($req->get('completed'));
        $entityManager->flush();

        $jsonContent = $Normalizer->normalize($task, 'json', ['groups' => 'tasks']);
        return new Response("Task updated successfully " . json_encode($jsonContent));
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
    #[Route("/deleteTaskJSON/{id}/{workspaceId}", name: "deleteTaskJSON")]
    public function deleteTaskJSON($id, NormalizerInterface $Normalizer, EntityManagerInterface $entityManager)
    {

        $task = $entityManager->getRepository(Task::class)->find($id);
        $entityManager->remove($task);
        $entityManager->flush();
        $jsonContent = $Normalizer->normalize($task, 'json', ['groups' => 'tasks']);
        return new Response("Task deleted successfully " . json_encode($jsonContent));
    }
}
