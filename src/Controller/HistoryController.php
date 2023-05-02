<?php

namespace App\Controller;

use App\Entity\History;
use App\Repository\HistoryRepository; 
use App\Form\HistoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//use Doctrine\Persistence\ManagerRegistry;

#[Route('/history')]
class HistoryController extends AbstractController
{
    #[Route('/', name: 'app_history_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $histories = $entityManager
            ->getRepository(History::class)
            ->findAll();

        return $this->render('history/index.html.twig', [
            'histories' => $histories,
        ]);
    }

    #[Route('/new', name: 'app_history_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $history = new History();
        $form = $this->createForm(HistoryType::class, $history);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($history);
            $entityManager->flush();

            return $this->redirectToRoute('app_history_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('history/new.html.twig', [
            'history' => $history,
            'form' => $form,
        ]);
    }

    #[Route('/{idHistory}', name: 'app_history_show', methods: ['GET'])]
    public function show(History $history): Response
    {
        return $this->render('history/show.html.twig', [
            'history' => $history,
        ]);
    }

    #[Route('/{idHistory}/edit', name: 'app_history_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, History $history, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HistoryType::class, $history);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_history_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('history/edit.html.twig', [
            'history' => $history,
            'form' => $form,
        ]);
    }

    #[Route('/{idHistory}', name: 'app_history_delete', methods: ['POST'])]
    public function delete(Request $request, History $history, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$history->getIdHistory(), $request->request->get('_token'))) {
            $entityManager->remove($history);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_history_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/f/{id}', name: 'app_history_id', methods: ['GET'])]

    //,ManagerRegistry $doctrine
    public function gethistory($id, Request $req, HistoryRepository $repo): Response
    {
        $histories = $repo->findByFreelancerId($id); 
        //$test = $student->getName(); 
        
        return $this->render('history/index.html.twig', [
            //'controller_name' => 'StudentController',
            'histories' => $histories,
        ]);
        
        
        /*$form = $this->createForm(StudentType::class, $student); 
        $form->handleRequest($req); 

        $em= $doctrine->getManager(); 

        if($form->isSubmitted()){
            $don = $form->getName(); 
            $em->flush();
            return $this->redirectToRoute('app_student'); 
        }

        return $this->render('student/updatestudent.html.twig', [
            'form'=>$form->createView()
        ]);
        */
    }
}
