<?php

namespace App\Controller;

use App\Entity\Test;
use App\Repository\TestRepository;
use App\Repository\QuestionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Dompdf\Dompdf;
use Dompdf\Options;
use Egulias\EmailValidator\Parser\DomainPart;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class ListOfTestsController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private Dompdf $dompdf;

    public function __construct(EntityManagerInterface $entityManager)
{
    $this->entityManager = $entityManager;
    $this->dompdf = new Dompdf();
}


#[Route('/tests', name: 'app_tests')]
public function index(TestRepository $testRepository): Response
{
    $tests = $testRepository->createQueryBuilder('t')
        ->orderBy('t.name', 'ASC')
        ->getQuery()
        ->getResult();
    
    return $this->render('list_of_tests/index.html.twig', [
        'controller_name' => 'ListOfTestsController',
        'tests' => $tests,
    ]);
}

    #[Route('/passtest/{id}',name:'app_passtest', methods: ['GET'])]
public function pass(Test $test, QuestionRepository $questionRepository):Response
{
    $questions = $questionRepository->findByTestId($test->getIdTest());

    return $this->render('list_of_tests/pass.html.twig', [
        'test' => $test,
        'questions' => $questions,
    ]);
}
#[Route('/submit-answers/{id}', name: 'app_submit_answers', methods: ['POST'])]
public function submitAnswers(Request $request, Test $test, QuestionRepository $questionRepository,SessionInterface $session): Response
{
    $userAnswers = $request->request->all();
    $questions = $questionRepository->findByTestId($test->getIdTest());

    
    $score = 0;
    $correctAnswers = 0;

    
    foreach ($questions as $question) {
        $userAnswer = $userAnswers['answer_'.$question['idQuestion']];
        if ($userAnswer === $question['response']) {
            $score++;
            $correctAnswers++;
        } else {
        }
    }

    $finalScore = round(($score / count($questions)) * 100);
    $session = $request->getSession();
    $session->set('userAnswers', $userAnswers);
    $session->set('score', $score);
    $session->set('correctAnswers', $correctAnswers);

    return $this->render('list_of_tests/results.html.twig', [
        'test' => $test,
        'questions' => $questions,
        'userAnswers' => $userAnswers,
        'finalScore' => $finalScore,
        'correctAnswers' => $correctAnswers,
    ]);
}
#[Route('/generate-pdf/{id}', name: 'app_generate_pdf', methods: ['GET'])]
public function generatePdf(Request $request,Test $test, QuestionRepository $questionRepository): Response
{
    $questions = $questionRepository->findByTestId($test->getIdTest());

    $session = $request->getSession();
    $userAnswers = $session->get('userAnswers');
    $score = $session->get('score');
    $correctAnswers = $session->get('correctAnswers');

    foreach ($questions as $question) {
        $userAnswer = $request->request->get('answer_'.$question['idQuestion']);
        if ($userAnswer === $question['response']) {
            $score++;
            $correctAnswers++;
        }
        $userAnswers[$question['quest']] = $userAnswer;
    }

    $finalScore = round(($score / count($questions)) * 100);

    $html = $this->renderView('list_of_tests/pdf_template.html.twig', [
        'test' => $test,
        'questions' => $questions,
        'userAnswers' => $userAnswers,
        'finalScore' => $finalScore,
        'correctAnswers' => $correctAnswers,
    ]);

    $options = new Options();
    $options->set('defaultFont', 'Arial');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $pdfOutput = $dompdf->output();
    $filename = $test->getName() . ' Results.pdf';

    $response = new Response($pdfOutput);
    $response->headers->set('Content-Type', 'application/pdf');
    $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
    $response->headers->set('Content-Length', strlen($pdfOutput));

    return $response;
}

#[Route('/recherche_ajax', name: 'recherche_ajax_test')]
    public function rechercheAjax(Request $request): JsonResponse
    {
        $requestString = $request->query->get('searchValue');
        
        $resultats = $this->entityManager
        ->createQuery(
            'SELECT t
            FROM App\Entity\Test t
            WHERE t.name LIKE  :name')
        ->setParameter('name', '%'.$requestString.'%' )
        ->getArrayResult();
        return $this->json($resultats);
    }


}
