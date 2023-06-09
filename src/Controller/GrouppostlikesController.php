<?php

namespace App\Controller;

use App\Entity\Grouppostlikes;
use App\Form\GrouppostlikesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;


#[Route('/grouppostlikes')]
class GrouppostlikesController extends AbstractController
{
    #[Route('/', name: 'app_grouppostlikes_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $grouppostlikes = $entityManager
            ->getRepository(Grouppostlikes::class)
            ->findAll();

        return $this->render('grouppostlikes/index.html.twig', [
            'grouppostlikes' => $grouppostlikes,
        ]);
    }

    #[Route('/new', name: 'app_grouppostlikes_new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $session = $request->getSession();
        $user= $session->get('user');
        $grouppostlike = new Grouppostlikes();
        $body = $request->getContent();
        $data = json_decode($body, true);
        $grouppostlike->setIdgrouppost($data['Idgrouppost']);
        $grouppostlike->setIduser($user->getIdUser());
        $grouppostlike->setIdgroup($data['Idgroup']);
        $countLike=$entityManager
        ->getRepository(Grouppostlikes::class)->createQueryBuilder('m') ->where('m.iduser = :userId')
    ->andWhere('m.idgrouppost = :grouppostId')->setParameter('userId', $user->getIdUser())
    ->setParameter('grouppostId', $data['Idgrouppost']) ->select('COUNT(m.iduser)')
    ->getQuery()
    ->getSingleScalarResult();
    if ($countLike > 0) {
            throw new HttpException(400, 'you can t add like twice');
        }
        $entityManager->persist($grouppostlike);
        $entityManager->flush();
        return new JsonResponse($countLike);
    }

    // #[Route('/delete', name: 'app_grouppostlikes_delete', methods: ['POST'])]
    // public function delete(
    //     Request $request,
    //     Grouppostlikes $grouppostlike,
    //     EntityManagerInterface $entityManager
    // ): Response {
    //     $grouppostlike->getIdgrouppost($data['Idgrouppost']);
    //     $grouppostlike->getIduser($data['Iduser']);
    //     $grouppostlike->getIdgroup($data['Idgroup']);
    //     $entityManager->remove($grouppostlike);
    //     $entityManager->flush();
    //     return new JsonResponse($data);
    // }

    #[Route('/{idgrouppost}/{iduser}/delete', name: 'app_grouppostlikes_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Grouppostlikes $grouppostlike,
        int $idgrouppost,
        int $iduser,
        EntityManagerInterface $entityManager,
        
    ): Response {
        $grouppostlike = $entityManager->getRepository(Grouppostlikes::class)->findOneBy([
         'idgrouppost' => $idgrouppost,
           'iduser' => $iduser,
        ]);
        $entityManager->remove($grouppostlike);
        $entityManager->flush();
        return new JsonResponse([]);
    }

    #[Route('/{idgrouppost}/edit', name: 'app_grouppostlikes_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Grouppostlikes $grouppostlike,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(GrouppostlikesType::class, $grouppostlike);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute(
                'app_grouppostlikes_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('grouppostlikes/edit.html.twig', [
            'grouppostlike' => $grouppostlike,
            'form' => $form,
        ]);
    }
}
