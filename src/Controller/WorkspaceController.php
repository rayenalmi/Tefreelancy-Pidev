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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use TCPDF;
use Twilio\Rest\Client;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Role\Role;

use Symfony\Component\HttpFoundation\Session\Session;

require_once __DIR__ . '../../../vendor/autoload.php'; // load Composer's autoloader

use Symfony\Component\Dotenv\Dotenv;


#[Route('/workspace')]
class WorkspaceController extends AbstractController
{
    private $workspaceId;


    #[Route('/', name: 'app_workspace_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager, WorkspaceRepository $repo3): Response
    {
        /* $workspaces = $entityManager
            ->getRepository(Workspace::class)
            ->findBy([], ['id' => 'DESC']); */

        $session = $request->getSession();
        $u = $session->get('user')->getIdUser();
        /* $workspaces = $repo3->getWorkspacesForFreelancer(12);
        $freelancer_id = 12; */
        $workspaces = $repo3->getWorkspacesForFreelancer($u);
        $freelancer_id = $u;
        $the_freelancer = $repo3->getFreelancerById($freelancer_id);
        $role = $the_freelancer->getRole();
        return $this->render('workspace/index.html.twig', [
            'workspaces' => $workspaces,
            'freelancer_role' => $role
        ]);
    }

    #[Route("/AllWsForFreeJson", name: "WsJsonFree")]
    public function getAllWsJson(Request $request, WorkspaceRepository $repo, SerializerInterface $serializer)
    {
        $session = $request->getSession();
        $u = $session->get('user')->getIdUser();
        $workspaces = $repo->getWorkspacesForFreelancer($u); // change
        $json = $serializer->serialize($workspaces, 'json', ['groups' => "workspaces"]);
        return new Response($json);
    }


    #[Route('/pdf-example/{id}', name: 'pdf_save', methods: ['GET'])]
    public function downloadTasks($id, TaskRepository $repo)
    {
        // Create a new TCPDF instance
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Set document properties
        $pdf->SetCreator('Wassim');
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Tefreelancy');
        $pdf->SetSubject('Workspace Tasks');

        // Add a new page
        $pdf->AddPage();

        $tasks = $repo->getTasksForWorkspace($id);

        // Render the task list as HTML
        $html = $this->renderView('home_ws/pdf_template.html.twig', [
            'tasks' => $tasks,
        ]);

        $pdf->Image("C:\Users\USER\Desktop\SprintWeb\Tefreelancy-Pidev\public\assets\img\logofinal.jpg", 15, 140, 180, 80, 'JPG', '', '', true, 0, '', false, false, 0, false, false, false);

        // Output the HTML to the PDF document
        $pdf->writeHTML($html, false, false, true, false, '');
        $pdf->SetFont('helvetica', 'B', 20);

        // add a page
        $pdf->AddPage();
        $pdf->Write(0, 'Tasks Progress');
        $numCompletedTasks = 0;
        $numProgressTasks = 0;

        foreach ($tasks as $task) {
            if ($task->isCompleted() == true) {
                $numCompletedTasks++;
            }
        }

        foreach ($tasks as $task) {
            if ($task->isCompleted() == false) {
                $numProgressTasks++;
            }
        }


        // Write chart and labels
        $numCompletedTasks = 0;
        $numProgressTasks = 0;

        foreach ($tasks as $task) {
            if ($task->isCompleted() == true) {
                $numCompletedTasks++;
            } else {
                $numProgressTasks++;
            }
        }

        // Number of tasks done and undone
        $tasksDone = $numCompletedTasks;
        $tasksUndone = $numProgressTasks;

        // Pie chart settings
        $xc = 105;
        $yc = 100;
        $r = 50;

        // Calculate angles for tasks done and undone
        $angleDone = 360 * $tasksDone / ($tasksDone + $tasksUndone);
        $angleUndone = 360 - $angleDone;

        // Draw pie sectors
        $pdf->SetFillColor(0, 255, 0); // green for tasks done
        $pdf->PieSector($xc, $yc, $r, 0, $angleDone, 'FD', false, 0, 2);

        $pdf->SetFillColor(255, 0, 0); // red for tasks undone
        $pdf->PieSector($xc, $yc, $r, $angleDone, 360, 'FD', false, 0, 2);

        // Write labels
        $pdf->SetTextColor(0, 0, 0); // black text
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Text(105, 90, '  Tasks Done: ' . $tasksDone);
        $pdf->Text(105, 110, 'Tasks Undone: ' . $tasksUndone);

        $pdf->Image("C:\Users\USER\Desktop\SprintWeb\Tefreelancy-Pidev\public\assets\img\logofinal.jpg", 15, 140, 180, 80, 'JPG', '', '', true, 0, '', false, false, 0, false, false, false);

        // Get the PDF content as a string
        $pdfContent = $pdf->Output('', 'S');

        // Create a Response object to return the PDF content
        $response = new Response($pdfContent);

        // Set the response headers
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="workspace.pdf"');

        return $response;
    }




    #[Route('/homews/{id}', name: 'app_workspace_homews', methods: ['GET', 'POST'])]
    public function homeWs(Request $request, $id, EntityManagerInterface $entityManager, TaskRepository $repo, PublicationWsRepository $repo2, WorkspaceRepository $repo3): Response
    {
        $search = $request->query->get('search');

        $tasks = $repo->getTasksForWorkspace($id);
        $workspaces = $entityManager
            ->getRepository(Workspace::class)
            ->findAll();
        $freelancers = $repo3->getFreelancersForWorkspace($id);
        $checkbox = $this->createFormBuilder()
            ->add('toggle', CheckboxType::class, ['label' => 'Toggle', 'required' => false])
            ->getForm();
        $publicationWs = $repo2->getPublicationWssForWorkspace($id);
        $lastPost = $repo2->getLastPost($id);

        // if search query is set, get the search results and pass them to the view
        if ($search) {
            $tasks = $repo->findBySearchQuery($search);
        }

        $session = $request->getSession();
        $u = $session->get('user')->getIdUser();
        $freelancer_id = $u; //change
        $the_freelancer = $repo3->getFreelancerById($freelancer_id);
        $role = $the_freelancer->getRole();

        return $this->render('home_ws/index.html.twig', [
            'workspaces' => $workspaces,
            'publication_ws' => $publicationWs,
            'workspaceId' => $id,
            'tasks' => $tasks,
            'freelancer_role' => $role,
            'freelancers' => $freelancers,
            'lastFilter' => $lastPost,
            'check' => $checkbox->createView(),
            'searchQuery' => $search // pass the search query to the view
        ]);
    }


    /**
     * @Route("/freelancers/{id}/remove/{workspaceId}", name="remove_freelancer")
     */
    public function remove($id, $workspaceId, EntityManagerInterface $entityManager, WorkspaceRepository $repo3): Response
    {
        $workspaceFreelancer = $entityManager->getRepository(WorkspaceFreelancer::class)
            ->findOneBy(['workspaceId' => $workspaceId, 'freelancerId' => $id]);

        if ($workspaceFreelancer) {
            $entityManager->remove($workspaceFreelancer);
            $entityManager->flush();
        }

        // redirect to edit workspace page
        return $this->redirectToRoute('app_editworkspace', [
            'id' => $workspaceId
        ]);
    }

    #[Route('/editws/{id}', name: 'app_editworkspace', methods: ['GET', 'POST'])]
    public function editWorkSpace(PaginatorInterface $paginator, PaginatorInterface $paginator2, Request $request, $id, EntityManagerInterface $entityManager, TaskRepository $repo, PublicationWsRepository $repo2, WorkspaceRepository $repo3): Response
    {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '../../../.env');

        $workspaces = $entityManager
            ->getRepository(Workspace::class)
            ->findAll();
        $freelancers = $repo3->getFreelancersForWorkspace($id);

        $publicationWs = $repo2->getPublicationWssForWorkspace($id);

        $tasks = $repo->getTasksForWorkspace($id);

        $form2 = $this->createForm(AddFreelancerWsType::class);
        $form2->handleRequest($request);
        $newFreelancer = new User();
        if ($form2->isSubmitted() && $form2->isValid()) {
            $formData = $form2->getData();
            $email = $formData['email'];

            $newFreelancer = $repo3->getFreelancerByEmail($email);
            $workspaceFreelancer = new WorkspaceFreelancer();
            $workspaceFreelancer->setWorkspaceId($id);
            $workspaceFreelancer->setFreelancerId($newFreelancer->getIdUser());
            $entityManager->persist($workspaceFreelancer);


            $entityManager->flush();
        }

// Twilio

        // Get Twilio credentials from environment variables
        $accountSid = "AC41ddc35f4041bedc6be015a1570ecb81";
        $authToken = "e0e7d9b8b6b0698fe99bc42c92560a7d";

        // Initialize the Twilio client with your account SID and auth token
        $client = new Client($accountSid, $authToken);

        // The Twilio phone number you want to send the message from
        $fromNumber = '+13203628745';


        // The phone number you want to send the message to
        $toNumber = '+216' . $newFreelancer->getPhone();


        // The message you want to send
        $messageBody = "Hello Freelancer ,You have been added to this Workspace";

        // Send the message using the Twilio API
        $message = $client->messages->create($toNumber, [
            'from' => $fromNumber,
            'body' => $messageBody,
        ]);

        // end Twilio

        $pagination = $paginator->paginate(
            $publicationWs,
            $request->query->getInt('page', 1),
            3
        );

        $pagination2 = $paginator2->paginate(
            $tasks,
            $request->query->getInt('page', 1),
            3
        );

        array_push($freelancers, $newFreelancer);
        return $this->render('home_ws/edit.html.twig', [
            'workspaces' => $workspaces,
            'pagination' => $pagination,
            'pagination2' => $pagination2,
            'publication_ws' => $publicationWs,
            'workspaceId' => $id,
            'tasks' => $tasks,
            'freelancers' => $freelancers,
            'form2' => $form2->createView()
        ]);
    }

    #[Route('/new', name: 'app_workspace_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $workspace = new Workspace();
        $form = $this->createForm(WorkspaceType::class, $workspace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingWs = $entityManager->getRepository(Workspace::class)->findOneBy(['name' => $workspace->getName()]);
            if ($existingWs != null) {
                $message = 'Workspace already exists';
                return $this->renderForm('workspace/new.html.twig', [
                    'workspace' => $workspace,
                    'form' => $form,
                    'message' => $message
                ]);
            }
            $entityManager->persist($workspace);
            $entityManager->flush();


            // Add the workspace task

            $session = $request->getSession();
            $u = $session->get('user')->getIdUser();
            $workspaceFreelancer = new WorkspaceFreelancer();
            $workspaceFreelancer->setWorkspaceId($workspace->getId());
            $workspaceFreelancer->setFreelancerId($u); // change
            $entityManager->persist($workspaceFreelancer);
            $entityManager->flush();

            return $this->redirectToRoute('app_workspace_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('workspace/new.html.twig', [
            'workspace' => $workspace,
            'form' => $form,
        ]);
    }

    #[Route("addWsJSON/new", name: "addWsJSON")]
    public function addWsJSON(Request $req,   NormalizerInterface $Normalizer, EntityManagerInterface $entityManager)
    {

        $workspace = new Workspace();
        $workspace->setName($req->get('name'));
        $workspace->setDescription($req->get('description'));

        $entityManager->persist($workspace);
        $entityManager->flush();

        $session = $req->getSession();
        $u = $session->get('user')->getIdUser();

        // Add the workspace task
        $workspaceFreelancer = new WorkspaceFreelancer();
        $workspaceFreelancer->setWorkspaceId($workspace->getId());
        $workspaceFreelancer->setFreelancerId($u); // change
        $entityManager->persist($workspaceFreelancer);

        $jsonContent = $Normalizer->normalize($workspace, 'json', ['groups' => 'workspaces']);
        return new Response(json_encode($jsonContent));
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
            $existingWs = $entityManager->getRepository(Workspace::class)->findOneBy(['name' => $workspace->getName()]);
            if ($existingWs != null) {
                $message = 'Workspace already exists';
                return $this->renderForm('workspace/new.html.twig', [
                    'workspace' => $workspace,
                    'form' => $form,
                    'message' => $message
                ]);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_workspace_index', [], Response::HTTP_SEE_OTHER);
        }



        return $this->renderForm('workspace/edit.html.twig', [
            'workspace' => $workspace,
            'form' => $form
        ]);
    }

    #[Route('/{id}/editJson', name: 'workspace_editJson', methods: ['GET', 'POST'])]
    public function updateWsJson($id, Request $req, NormalizerInterface $Normalizer, Workspace $workspace, EntityManagerInterface $entityManager): Response
    {

        $workspace = $entityManager->getRepository(Workspace::class)->find($id);
        $workspace->setName($req->get('name'));
        $workspace->setDescription($req->get('description'));

        $entityManager->flush();

        $jsonContent = $Normalizer->normalize($workspace, 'json', ['groups' => 'workspaces']);
        return new Response("Workspace updated successfully " . json_encode($jsonContent));
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


    #[Route("/deleteWsJSON/{id}/{workspaceId}", name: "deleteWsJSON")]
    public function deleteWsJSON($id, NormalizerInterface $Normalizer, EntityManagerInterface $entityManager)
    {

        $workspace = $entityManager->getRepository(Workspace::class)->find($id);
        $entityManager->remove($workspace);
        $entityManager->flush();
        $jsonContent = $Normalizer->normalize($workspace, 'json', ['groups' => 'workspaces']);
        return new Response("Task deleted successfully " . json_encode($jsonContent));
    }
}
