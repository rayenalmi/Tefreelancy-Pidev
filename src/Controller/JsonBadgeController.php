<?php

namespace App\Controller;

use App\Entity\Badge;
use App\Form\BadgeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


#[Route('/badgeJSON')]
class JsonBadgeController extends AbstractController
{
    #[Route('/', name: 'app_badge_indexJSON')]
    public function indexJSON(EntityManagerInterface $entityManager,NormalizerInterface $normalizer)
    {
        $badges = $entityManager
            ->getRepository(Badge::class)
            ->findAll();

            $badgesNormalises = $normalizer->normalize($badges,'json',['groups' => 'badges']);
            $json = json_encode($badgesNormalises);
            return new Response($json);
    }

    #[Route('/new', name: 'app_badge_newJSON')]
    public function newJSON(Request $request, EntityManagerInterface $entityManager,NormalizerInterface $normalizer)
    {
        $badge = new Badge();
        $badge->setName($request->get('name'));
        $badge->setType($request->get('type'));
        $badge->setImage($request->get('image'));
        $badge->setIdTest($request->get('id_test'));
        $entityManager->persist($badge);
        $entityManager->flush();
        $jsonContent = $normalizer->normalize($badge,'json',['groups'=> 'badges']);
        return new Response(json_encode($jsonContent)); 
    }

    #[Route('/{idBadge}/edit', name: 'app_badge_editJSON')]
    public function editJSON(Request $request, $idBadge, EntityManagerInterface $entityManager,NormalizerInterface $normalizer)
    {
        $badge=$entityManager->getRepository(Badge::class)->find($idBadge);
        $badge->setName($request->get('name'));
        $badge->setType($request->get('type'));
        $badge->setImage($request->get('image'));
        $badge->setIdTest($request->get('id_test'));
        $entityManager->flush();
        $jsonContent = $normalizer->normalize($badge,'json',['groups'=> 'badges']);
        return new Response(json_encode($jsonContent));    
    }

    #[Route('/{idBadge}/delete', name: 'app_badge_deleteJSON')]
    public function deleteJSON( $idBadge, EntityManagerInterface $entityManager,NormalizerInterface $normalizer)
    {
        $badge = $entityManager->getRepository(Badge::class)->find($idBadge);
        $entityManager->remove($badge);
        $entityManager->flush();
        $jsonContent = $normalizer->normalize($badge,'json',['groups'=>'badges']);
        return new Response("Test deleted successfully".json_encode($jsonContent));
    }
}
