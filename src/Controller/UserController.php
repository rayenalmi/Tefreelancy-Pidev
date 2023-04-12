<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserTypeNew;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;


#[Route('/user')]
class UserController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getUsersByEmail(string $email): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT u FROM App\Entity\User u WHERE u.email = :email'
        )->setParameter('email', $email);

        return $query->getResult();
    }

    public function getUsersByRole(string $role): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT u FROM App\Entity\User u WHERE u.role = :role'
        )->setParameter('role', $role);

        return $query->getResult();
    }
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(): Response
    {   
        $session = new Session(); 
        $session->start(); 
        //$session->get('key');
        $freelancers = $this->getUsersByRole("freelancer");
        $recruters= $this->getUsersByRole("recruter");

        return $this->render('user/index.html.twig', [
            'freelancers' => $freelancers,
            'recruters' => $recruters,
            't' => $session->get('id')
        ]);
    }

    #[Route('/newRectuter', name: 'app_user_newR', methods: ['GET', 'POST'])]
    public function newR(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $user->setRole("recruter");
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $u =$this->getUsersByEmail($form["email"]->getData());
            if (count($u)!=0) 
            {
                return $this->redirectToRoute('app_user_newR', [], Response::HTTP_SEE_OTHER);
            }
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    #[Route('/newFreelancer', name: 'app_user_newF', methods: ['GET', 'POST'])]
    public function newF(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $user->setRole("freelancer");
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $u =$this->getUsersByEmail($form["email"]->getData());
            if (count($u)!=0)
            {
                return $this->redirectToRoute('app_user_newR', [], Response::HTTP_SEE_OTHER);
            }
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{idUser}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{idUser}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{idUser}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getIdUser(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
