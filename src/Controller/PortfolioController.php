<?php

namespace App\Controller;

use App\Entity\Achievement;
use App\Entity\Portfolio;
use App\Form\PortfolioType;
use App\Repository\AchievementRepository;
use App\Repository\ExperienceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



//require_once 'vendor/autoload.php'; 
//require_once 'includes/connect.php'; 

use Dompdf\Dompdf; 


use Dompdf\Options; 


use App\Repository\PortfolioRepository;
use App\Repository\ProjectRepository;
use App\Repository\SkillsRepository;

#[Route('/portfolio')]
class PortfolioController extends AbstractController
{
    #[Route('/', name: 'app_portfolio_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $portfolios = $entityManager
            ->getRepository(Portfolio::class)
            ->findAll();

        return $this->render('portfolio/index.html.twig', [
            'portfolios' => $portfolios,
        ]);
    }

    #[Route('/new', name: 'app_portfolio_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $portfolio = new Portfolio();
        $form = $this->createForm(PortfolioType::class, $portfolio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($portfolio);
            $entityManager->flush();

            return $this->redirectToRoute('app_portfolio_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('portfolio/new.html.twig', [
            'portfolio' => $portfolio,
            'form' => $form,
        ]);
    }

    #[Route('/{idPortfolio}', name: 'app_portfolio_show', methods: ['GET'])]
    public function show(Portfolio $portfolio): Response
    {
        return $this->render('portfolio/show.html.twig', [
            'portfolio' => $portfolio,
        ]);
    }

    #[Route('/{idPortfolio}/edit', name: 'app_portfolio_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Portfolio $portfolio, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PortfolioType::class, $portfolio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_portfolio_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('portfolio/edit.html.twig', [
            'portfolio' => $portfolio,
            'form' => $form,
        ]);
    }

    #[Route('/{idPortfolio}', name: 'app_portfolio_delete', methods: ['POST'])]
    public function delete(Request $request, Portfolio $portfolio, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$portfolio->getIdPortfolio(), $request->request->get('_token'))) {
            $entityManager->remove($portfolio);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_portfolio_index', [], Response::HTTP_SEE_OTHER);
    }



    // public function findByFreelancerId($value): array
    //     {
    //         return $this->createQueryBuilder('s')
    //             ->andWhere('s.idFreelancer = :val')
    //             ->setParameter('val', $value)
    //             //->orderBy('s.id', 'ASC')
    //             ->setMaxResults(10)
    //             ->getQuery()
    //             ->getResult()
    //         ;
    //     }
    #[Route('/f/{id}', name: 'app_portfolio_byfreelancer', methods: ['GET'])]
    //public function getfreelancer($id, EntityManagerInterface $entityManager): Response
    public function getf($id, PortfolioRepository $repo): Response
    
    {
        $portfolio = $repo->findByFreelancerId($id); 
        return $this->render('portfolio/indexid.html.twig', [
            'portfolioid' => $portfolio,
            
            //'final_budget' => $finalbudget,
        ]);
    }



    #[Route('/f/{id}/pdf', name: 'app_portfolio_byfreelancer_pdf', methods: ['GET'])]
    public function getf1($id, PortfolioRepository $repo, SkillsRepository $repo_skills,
    ExperienceRepository $repo_exp, AchievementRepository $repo_achiv, ProjectRepository $repo_proj
    ): Response

    {
        $portfolio = $repo->findByFreelancerId($id); 
        $skills = $repo_skills->findByFreelancerId($id); 
        $experiences = $repo_exp->findByFreelancerId($id); 
        $achievements = $repo_achiv->findByFreelancerId($id); 
        $projects = $repo_proj->findByFreelancerId($id); 
        

        // Generate the PDF
       //$dompdf = new Dompdf(); 

         $options = new Options();
    $options->set('defaultFont', 'Courier');
    $dompdf = new Dompdf($options);


       $html = $this->renderView('portfolio/pdf.html.twig', [
        'portfolioid' => $portfolio,
        'skillsid' => $skills,
        'experiencesid' => $experiences,
        'achievementsid' => $achievements,
        'projectsid' =>$projects,
    ]);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream(); 

    // $response = new Response($dompdf->output());
    // $disposition = $response->headers->makeDisposition(
    //     ResponseHeaderBag::DISPOSITION_ATTACHMENT,
    //     'test_result.pdf'
    // );
    // $response->headers->set('Content-Disposition', $disposition);

    // return $response;
    






        return $this->render('portfolio/pdf.php', [
            'portfolioid' => $portfolio,
            'skillsid' => $skills,
            'experiencesid' => $experiences,
            'achievementsid' => $achievements,
            'projectsid' =>$projects,
            
            //'final_budget' => $finalbudget,
        ]);
   
    }





}
