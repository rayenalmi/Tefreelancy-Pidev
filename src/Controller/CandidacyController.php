<?php

namespace App\Controller;

use App\Entity\Candidacy;
use App\Form\CandidacyType;
use App\Form\CandidacyTypeEdit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/start/candidacy')]
class CandidacyController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function getCandidacyByID(int $id): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT c.idCandidacy,c.object,c.message,c.accepted FROM App\Entity\Candidacy c  WHERE c.idFreelancer = :id'
        )->setParameter('id', $id);

        return $query->getResult();
    }


    #[Route('/', name: 'app_candidacy_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $u = $session->get('user');
        
        $candidacies = $this->getCandidacyByID($u->getIdUser());

        return $this->render('candidacy/index.html.twig', [
            'candidacies' => $candidacies,
        ]);
    }

    #[Route('/new', name: 'app_candidacy_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $candidacy = new Candidacy();
        $form = $this->createForm(CandidacyType::class, $candidacy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($candidacy);
            $entityManager->flush();

            return $this->redirectToRoute('app_candidacy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('candidacy/new.html.twig', [
            'candidacy' => $candidacy,
            'form' => $form,
        ]);
    }

    #[Route('/{idCandidacy}', name: 'app_candidacy_show', methods: ['GET'])]
    public function show(Candidacy $candidacy): Response
    {
        return $this->render('candidacy/show.html.twig', [
            'candidacy' => $candidacy,
        ]);
    }

    #[Route('/{idCandidacy}/edit', name: 'app_candidacy_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Candidacy $candidacy, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CandidacyTypeEdit::class, $candidacy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_candidacy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('candidacy/edit.html.twig', [
            'candidacy' => $candidacy,
            'form' => $form,
        ]);
    }

    #[Route('/{idCandidacy}', name: 'app_candidacy_delete', methods: ['POST'])]
    public function delete(Request $request, Candidacy $candidacy, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $candidacy->getIdCandidacy(), $request->request->get('_token'))) {
            $entityManager->remove($candidacy);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_candidacy_index', [], Response::HTTP_SEE_OTHER);
    }
}
