<?php

namespace App\Controller;

use App\Entity\Question;
use App\Form\QuestionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/questionJSON')]
class JsonQuestionController extends AbstractController
{
    #[Route('/', name: 'app_question_indexJSON')]
    public function indexJSON(EntityManagerInterface $entityManager,NormalizerInterface $normalizer): Response
    {
        $questions = $entityManager
            ->getRepository(Question::class)
            ->findAll();

            $QuestionsNormalises = $normalizer->normalize($questions,'json',['groups' => 'questions']);
            $json = json_encode($QuestionsNormalises);
            return new Response($json);
    }

    #[Route('/new', name: 'app_question_newJSON')]
    public function newJSON(Request $request, EntityManagerInterface $entityManager,NormalizerInterface $normalizer)
    {
        $question = new Question();
        $question->setQuest($request->get('quest'));
        $question->setChoice1($request->get('choice1'));
        $question->setChoice2($request->get('choice2'));
        $question->setChoice3($request->get('choice3'));
        $question->setResponse($request->get('response'));
        $question->setIdTest($request->get('idTest'));
        $entityManager->persist($question);
        $entityManager->flush();
        $jsonContent = $normalizer->normalize($question,'json',['groups'=> 'questions']);
        return new Response(json_encode($jsonContent)); 
    }

    #[Route('/edit/{idQuestion}', name: 'app_question_editJSON')]
    public function editJSON(Request $request, int $idQuestion, EntityManagerInterface $entityManager,NormalizerInterface $normalizer)
    {
        $question=$entityManager->getRepository(Question::class)->find($idQuestion);
        $question->setQuest($request->get('quest'));
        $question->setChoice1($request->get('choice1'));
        $question->setChoice2($request->get('choice2'));
        $question->setChoice3($request->get('choice3'));
        $question->setResponse($request->get('response'));
        $question->setIdTest($request->get('idTest'));
        $entityManager->flush();
        $jsonContent = $normalizer->normalize($question,'json',['groups'=> 'questions']);
        return new Response(json_encode($jsonContent)); 
    }

    #[Route('/delete/{idQuestion}', name: 'app_question_deleteJSON')]
    public function deleteJSON(Request $request, int $idQuestion, EntityManagerInterface $entityManager,NormalizerInterface $normalizer)
    {
        $question = $entityManager->getRepository(Question::class)->find($idQuestion);
        $entityManager->remove($question);
        $entityManager->flush();
        $jsonContent = $normalizer->normalize($question,'json',['groups'=>'questions']);
        return new Response("Test deleted successfully".json_encode($jsonContent));
    }
}
