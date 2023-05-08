<?php

namespace App\Controller;

use App\Entity\Test;
use App\Form\TestType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


#[Route('/testJSON')]
class JsonTestController extends AbstractController
{
    #[Route('/', name: 'app_test_indexJSON',methods: ['GET'])]
    public function indexJSON(EntityManagerInterface $entityManager,NormalizerInterface $normalizer)
    {
        $tests = $entityManager
            ->getRepository(Test::class)
            ->findAll();

        $TestsNormalises = $normalizer->normalize($tests,'json',['groups' => "tests"]);
        $json = json_encode($TestsNormalises);
        return new Response($json);
    }

    #[Route('/new', name: 'app_test_newJSON', methods: ['GET', 'POST'])]
    public function newJSON(Request $req, EntityManagerInterface $entityManager,NormalizerInterface $normalizer)
    {
        $test = new Test();
        $test->setName($req->get('name'));
        $test->setType($req->get('type'));
        $entityManager->persist($test);
        $entityManager->flush();
        $jsonContent = $normalizer->normalize($test,'json',['groups'=> "tests"]);
        return new Response(json_encode($jsonContent));       
    }

    #[Route('/edit/{idTest}', name: 'app_test_editJSON', methods: ['GET', 'POST'])]
    public function editJSON(Request $request, int $idTest, EntityManagerInterface $entityManager, NormalizerInterface $normalizer)
    {
        $test = $entityManager->getRepository(Test::class)->find($idTest);
        $test->setName($request->get('name'));
        $test->setType($request->get('type'));
        $entityManager->flush();
        $jsonContent = $normalizer->normalize($test, 'json', ['groups' => "tests"]);
        return new Response(json_encode($jsonContent));
    }    
    #[Route('/delete/{idTest}', name: 'app_test_deleteJSON')]
    public function deleteJSON(Request $request, int $idTest, EntityManagerInterface $entityManager, NormalizerInterface $normalizer)
    {  
        $test = $entityManager->getRepository(Test::class)->find($idTest);
        $entityManager->remove($test);
        $entityManager->flush();
        $jsonContent = $normalizer->normalize($test, 'json', ['groups' => "tests"]);
        return new Response("Test deleted successfully". json_encode($jsonContent));
    }
    #[Route('/passtest/{id}',name:'app_passtestJSON', methods: ['GET'])]
    public function pass(Test $test,NormalizerInterface $Normalizer,QuestionRepository $questionRepository)
    {
        $questions = $questionRepository->findByTestId($test->getIdTest());
        $WsNormalises = $Normalizer->normalize($questions, 'json', ['groups' => "questions"]);
        return new Response(json_encode($WsNormalises));
        
    }

}