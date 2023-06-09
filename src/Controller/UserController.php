<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\LoginType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\String\Slugger\SluggerInterface;


#[Route('/user')]
class UserController extends AbstractController
{
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getUsersByEmail(string $email)
    {
        $query = $this->entityManager->createQuery(
            'SELECT u FROM App\Entity\User u WHERE u.email = :email'
        )->setParameter('email', $email);

        return $query->getOneOrNullResult();
    }

    public function getUsersByRole(string $role): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT u FROM App\Entity\User u WHERE u.role = :role'
        )->setParameter('role', $role);

        return $query->getResult();
    }

    #[Route('/getallusers', name: 'app_user_freelancermobile', methods: ['GET'])]
    public function getallFMobile()
    {   
        $freelancers = $this->entityManager->createQuery(
            'SELECT u FROM App\Entity\User u')
        ->getResult();

        //var_dump($freelancers);
        $users = [] ;
        foreach ( $freelancers as $userF) {
           $u = [
                'id' => $userF->getIdUser(),
                'fname' => $userF->getFirstName(),
                'lname' => $userF->getLastName(),
                'email' => $userF->getEmail(),
                'phone'=> $userF->getPhone(),
                'photo' =>$userF->getPhoto(),
                'role'=> $userF->getRole()
           ];
           $users [] = $u ;
        }

        $json = json_encode($users);
        
        $response = new JsonResponse($json, 200, [], true);
        return $response;
    }

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(): Response
    {   
        //$session = new Session(); 
        //$session->start(); 
        //$session->get('key');
        $freelancers = $this->getUsersByRole("freelancer");
        $recruters= $this->getUsersByRole("recruter");

        return $this->render('user/index.html.twig', [
            'freelancers' => $freelancers,
            'recruters' => $recruters,
        ]);
    }

    #[Route('/signup/{role}', name: 'app_user_signup', methods:  ['GET', 'POST'])]
    public function l($role, Request $request, EntityManagerInterface $entityManager,SluggerInterface $slugger , UserPasswordHasherInterface $passwordHasher ): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        if ($role == "recruter")
        {
        $user->setRole("recruter");
        }
        else 
        {
        $user->setRole("freelancer");
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $u =$this->getUsersByEmail($form["email"]->getData());
            if ($u) 
            {
                $this->addFlash('error', 'Your action!');
                return $this->redirectToRoute('app_user_signup', ["role" => $role], Response::HTTP_SEE_OTHER);
            }


            
             /** @var UploadedFile $brochureFile */
             $brochureFile = $form->get('photo')->getData();

             // this condition is needed because the 'brochure' field is not required
             // so the PDF file must be processed only when a file is uploaded
             
             if ($brochureFile) {
                 $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                 // this is needed to safely include the file name as part of the URL
                 $safeFilename = $slugger->slug($originalFilename);
                 $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
 
                 // Move the file to the directory where brochures are stored
                 try {
                     $brochureFile->move(
                         $this->getParameter('brochures_directory'),
                         $newFilename
                     );
                 } catch (FileException $e) {
                     // ... handle exception if something happens during file upload
                 }
 
                 // updates the 'brochureFilename' property to store the PDF file name
                 // instead of its contents
                 $user->setPhoto($newFilename);

                 $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                );
                $user->setPassword($hashedPassword);
             }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_signin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/signup.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    #[Route('/deleteUser', name: 'app_user_deleteMobile', methods: ['POST'])]
    public function deleteuser(Request $request,EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $id = $data['id'];
        $user = $this->entityManager->getRepository(User::class)->find($id);
        $entityManager->remove($user);
        $entityManager->flush();
        return new JsonResponse(['succes' => 'user deleted']);
    }

    #[Route('/login', name: 'app_user_login', methods: ['GET', 'POST'])]
    public function login(Request $request, EntityManagerInterface $entityManager,SluggerInterface $slugger , UserPasswordHasherInterface $passwordHasher ): Response
    {
        $user = new User();
        $form = $this->createForm(LoginType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $u =$this->getUsersByEmail($form["email"]->getData());
            if (!$u) 
            {
                $this->addFlash('email', 'Your action!');
                return $this->redirectToRoute('app_user_login', [], Response::HTTP_SEE_OTHER);
            }
            else 
            {   
                if ($passwordHasher->isPasswordValid($u, $form["password"]->getData() ))
                {
                    $session = new Session(); 
                    //$session->start(); 
                    $session->set('user', $u);
                    return $this->redirectToRoute('app_favoris_index', [], Response::HTTP_SEE_OTHER);
                }
                else 
                {
                    $this->addFlash('password', 'Your action!');
                    return $this->redirectToRoute('app_user_login', [], Response::HTTP_SEE_OTHER);
                }
                //$user->setPassword($form["password"]->getData());

                /*if(!($passwtest==$hashedPassword))
                {
                    // faux mot de passe
                    $this->addFlash('password', 'Your action!');
                    return $this->redirectToRoute('app_user_login', [], Response::HTTP_SEE_OTHER);
                }
                else
                {
                    // les information sont correct
                    return $this->redirectToRoute('app_start', [], Response::HTTP_SEE_OTHER);
                }*/


            }

            //return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/login.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/signupfreelancermobile', name: 'mob_signupfreelancermobile', methods: ['POST'])]
    public function signUpFreelancerMobile(Request $request, EntityManagerInterface $entityManager , UserPasswordHasherInterface $passwordHasher ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'];
        $password = $data['password'];
        $fname = $data['fname']; 
        $lname = $data['lname']; 
        $phone = $data['phone']; 

        $user = new User();
       
        $user->setRole("freelancer");
        $user->setFirstName($fname);
        $user->setLastName($lname);
        $user->setEmail($email);
        $user->setPassword($password);
        
            $u =$this->getUsersByEmail($email);
            if ($u) 
            {
                return new JsonResponse(['error' => 'email exsit']);
            }
                 $user->setPhoto("test.png");
                 $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                );
                $user->setPassword($hashedPassword);
             
                    $user->setPhone(intval($phone));
                    $entityManager->persist($user);
                    $entityManager->flush();

                return new JsonResponse(['succes' => 'welcome']);
    }

    #[Route('/signinmobile', name: 'mob_signin', methods: ['POST'])]
    public function signMobile(Request $request, EntityManagerInterface $entityManager , UserPasswordHasherInterface $passwordHasher ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'];
        $password = $data['password'];

        $u =$this->getUsersByEmail($email);
        if (!$u) 
            {
                return new JsonResponse(['error' => 'email does not exsit']);
            }
            else 
            {   
                if ($passwordHasher->isPasswordValid($u, $password ))
                {
                    //$session = new Session(); 
                    //$session->start(); 
                    $u =$this->getUsersByEmail($email);
                    $uarrya = [
                        'id' => $u->getIdUser(),
                        'fname' => $u->getFirstName(),
                        'lname' => $u->getLastName(),
                        'email' => $u->getEmail(),
                        'phone'=> $u->getPhone(),
                        'photo' =>$u->getPhoto(),
                        'role'=> $u->getRole()
                   ];
                   $json = json_encode($uarrya);
                   $response = new JsonResponse($json, 200, [], true);
                    
                    return $response;
                }
                else 
                {
                    return new JsonResponse(['error' => 'password incorrect']);
                }
            }

    }

    #[Route('/signin', name: 'app_user_signin', methods: ['GET', 'POST'])]
    public function signin(Request $request, EntityManagerInterface $entityManager,SluggerInterface $slugger , UserPasswordHasherInterface $passwordHasher ): Response
    {
        $session = $request->getSession();

        if($session->get('user') != null ) 
        {
            $session->remove('user');
        }
        //$session->start(); 
        //$session->remove('user');
        $user = new User();
        $form = $this->createForm(LoginType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $u =$this->getUsersByEmail($form["email"]->getData());
            if (!$u) 
            {
                $this->addFlash('email', 'Your action!');
                return $this->redirectToRoute('app_user_signin', [], Response::HTTP_SEE_OTHER);
            }
            else 
            {   
                if ($passwordHasher->isPasswordValid($u, $form["password"]->getData() ))
                {
                    //$session = new Session(); 
                    //$session->start(); 
                    $session->set('user', $u);
                    return $this->redirectToRoute('app_favoris_index', [], Response::HTTP_SEE_OTHER);
                }
                else 
                {
                    $this->addFlash('password', 'Your action!');
                    return $this->redirectToRoute('app_user_signin', [], Response::HTTP_SEE_OTHER);
                }
                //$user->setPassword($form["password"]->getData());

                /*if(!($passwtest==$hashedPassword))
                {
                    // faux mot de passe
                    $this->addFlash('password', 'Your action!');
                    return $this->redirectToRoute('app_user_login', [], Response::HTTP_SEE_OTHER);
                }
                else
                {
                    // les information sont correct
                    return $this->redirectToRoute('app_start', [], Response::HTTP_SEE_OTHER);
                }*/


            }

            //return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/signin.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/newRectuter', name: 'app_user_newR', methods: ['GET', 'POST'])]
    public function newR(Request $request, EntityManagerInterface $entityManager,SluggerInterface $slugger , UserPasswordHasherInterface $passwordHasher ): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $user->setRole("recruter");
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $u =$this->getUsersByEmail($form["email"]->getData());
            if ($u) 
            {
                $this->addFlash('error', 'Your action!');
                return $this->redirectToRoute('app_user_newR', [], Response::HTTP_SEE_OTHER);
            }


            
             /** @var UploadedFile $brochureFile */
             $brochureFile = $form->get('photo')->getData();

             // this condition is needed because the 'brochure' field is not required
             // so the PDF file must be processed only when a file is uploaded
             
             if ($brochureFile) {
                 $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                 // this is needed to safely include the file name as part of the URL
                 $safeFilename = $slugger->slug($originalFilename);
                 $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
 
                 // Move the file to the directory where brochures are stored
                 try {
                     $brochureFile->move(
                         $this->getParameter('brochures_directory'),
                         $newFilename
                     );
                 } catch (FileException $e) {
                     // ... handle exception if something happens during file upload
                 }
 
                 // updates the 'brochureFilename' property to store the PDF file name
                 // instead of its contents
                 $user->setPhoto($newFilename);

                 $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                );
                $user->setPassword($hashedPassword);
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
    public function newF(Request $request, EntityManagerInterface $entityManager,SluggerInterface $slugger , UserPasswordHasherInterface $passwordHasher ): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $user->setRole("freelancer");
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $u =$this->getUsersByEmail($form["email"]->getData());
            if ($u)
            {   
                $this->addFlash('error', 'Your action!');
                return $this->redirectToRoute('app_user_newR', [], Response::HTTP_SEE_OTHER);
            }
            
            
             /** @var UploadedFile $brochureFile */
             $brochureFile = $form->get('photo')->getData();

             // this condition is needed because the 'brochure' field is not required
             // so the PDF file must be processed only when a file is uploaded
             
             if ($brochureFile) {
                 $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                 // this is needed to safely include the file name as part of the URL
                 $safeFilename = $slugger->slug($originalFilename);
                 $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
 
                 // Move the file to the directory where brochures are stored
                 try {
                     $brochureFile->move(
                         $this->getParameter('brochures_directory'),
                         $newFilename
                     );
                 } catch (FileException $e) {
                     // ... handle exception if something happens during file upload
                 }
 
                 // updates the 'brochureFilename' property to store the PDF file name
                 // instead of its contents
                 $user->setPhoto($newFilename);
                 $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                );
                $user->setPassword($hashedPassword);
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

    #[Route('/logout', name: 'app_user_logout', methods: ['GET', 'POST'])]
    public function logout(): Response
    {
        $session = new Session();
        $session->remove('user');
        
        //$session->get('key');
        $freelancers = $this->getUsersByRole("freelancer");
        $recruters= $this->getUsersByRole("recruter");

        return $this->render('user/index.html.twig', [
            'freelancers' => $freelancers,
            'recruters' => $recruters,
        ]);

    }

    #[Route('/cscsc', name: 'app_user_bbbb', methods: ['GET', 'POST'])]
    public function sqcqs(Request $request, EntityManagerInterface $entityManager,SluggerInterface $slugger , UserPasswordHasherInterface $passwordHasher ): Response
    {
        $user = new User();
        $form = $this->createForm(LoginType::class, $user);
        $form->handleRequest($request);
        

        return $this->renderForm('user/signin.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{idUser}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager ,SluggerInterface $slugger, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                    
             /** @var UploadedFile $brochureFile */
             $brochureFile = $form->get('photo')->getData();

             // this condition is needed because the 'brochure' field is not required
             // so the PDF file must be processed only when a file is uploaded
             
             if ($brochureFile) {
                 $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                 // this is needed to safely include the file name as part of the URL
                 $safeFilename = $slugger->slug($originalFilename);
                 $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
 
                 // Move the file to the directory where brochures are stored
                 try {
                     $brochureFile->move(
                         $this->getParameter('brochures_directory'),
                         $newFilename
                     );
                 } catch (FileException $e) {
                     // ... handle exception if something happens during file upload
                 }
 
                 // updates the 'brochureFilename' property to store the PDF file name
                 // instead of its contents
                 $user->setPhoto($newFilename);
                 $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                );
                $user->setPassword($hashedPassword);
             }
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
