<?php

namespace App\Controller;

use App\Entity\Achievement;
use App\Entity\Portfolio;
use App\Entity\User;
use App\Form\PortfolioType;
use App\Repository\AchievementRepository;
use App\Repository\ExperienceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//use Symfony\Bridge\Doctrine\ManagerRegistry;
use Doctrine\Persistence\ManagerRegistry;

//require_once 'vendor/autoload.php'; 
//require_once 'includes/connect.php'; 

use Dompdf\Dompdf; 


use Dompdf\Options; 


use App\Repository\PortfolioRepository;
use App\Repository\ProjectRepository;
use App\Repository\SkillsRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/portfolioJSON')]
class PortfolioControllerJSON extends AbstractController
{
    //read
    #[Route('/', name: 'app_portfolio_indexJSON', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, NormalizerInterface $normalizer): Response
    {
        $portfolios = $entityManager
            ->getRepository(Portfolio::class)
            ->findAll();

        $portfolioNormalises = $normalizer->normalize($portfolios, 'json', ['groups' => "PORTFOLIO"]);

        $json = json_encode($portfolioNormalises);
        return new Response($json); 
    }
//create
    #[Route('/new', name: 'app_portfolio_newJSON', methods: ['GET', 'POST'])]
    public function new(Request $req,  NormalizerInterface $normalizer,ManagerRegistry $doctrine, EntityManagerInterface $entityManager)
    {
        $em = $doctrine->getManager(); 

//        $em = $this->$this->getDoctrine()->getManager();
        $portfolio = new Portfolio();
        
        //$portfolio->setIdFreelancer();
         //$portfolio->setIdFreelancer(190);
         //$user = new User(190,"Zaki","OA",19019019,"oazaki@gmail.com","190pass","no photo","freelancer");
        //  $user = new User();
        //  $user->setIdUser(190); 
         
         
        $user = $entityManager->getRepository(User::class)->find($req->get('id'));
        $portfolio->setIdFreelancer($user);
        $portfolio->setIntro($req->get('intro'));
        $portfolio->setAbout($req->get('about')); 
        $em->persist($portfolio); 
        $em->flush();

        $jsonContent = $normalizer->normalize($portfolio, 'json', ['groups' => "PORTFOLIO"]);
        return new Response (json_encode($jsonContent)); 

    }
//showbyidportfolio
    #[Route('/{idPortfolio}', name: 'app_portfolio_showJSON', methods: ['GET'])]
    public function show($idPortfolio, Normalizerinterface $normalizer , PortfolioRepository $repo): Response
    {
        $portfolio = $repo->find($idPortfolio);
        $portfolioNormalises = $normalizer->normalize($portfolio, 'json', ['groups' => "PORTFOLIO"]);
        return new Response (json_encode($portfolioNormalises));
        
    }
//update
    #[Route('/{idPortfolio}/edit', name: 'app_portfolio_editJSON', methods: ['GET', 'POST'])]
    public function edit(Request $req,$idPortfolio,  NormalizerInterface $normalizer,ManagerRegistry $doctrine,EntityManagerInterface $entityManager): Response
    {
        $em = $doctrine->getManager(); 

        $user = $entityManager->getRepository(User::class)->find($idPortfolio);
        

        $portfolio = $em->getRepository(Portfolio::class)->find($idPortfolio);
        $portfolio->setIdFreelancer($user);
        $portfolio->setIntro($req->get('intro'));
        $portfolio->setAbout($req->get('about')); 
        
        $em->flush();

        $jsonContent = $normalizer->normalize($portfolio, 'json', ['groups' => "PORTFOLIO"]);
        return new Response ( "Portfolio updated successfully" . json_encode($jsonContent)); 

    }
//delete
    #[Route('/{idPortfolio}/delete', name: 'app_portfolio_deleteJSON', methods: ['GET','POST'])]
    public function delete(Request $req,$idPortfolio,  NormalizerInterface $normalizer,ManagerRegistry $doctrine,EntityManagerInterface $entityManager): Response
    {
    
        $em = $doctrine->getManager(); 

        //$user = $entityManager->getRepository(User::class)->find(190);
        
        $portfolio = $em->getRepository(Portfolio::class)->find($idPortfolio);
        $em->remove($portfolio); 
        $em->flush();
        $jsonContent = $normalizer->normalize($portfolio, 'json', ['groups' => "PORTFOLIO"]);
        return new Response ( "Portfolio deleted successfully" . json_encode($jsonContent)); 

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
    #[Route('/f/{id}', name: 'app_portfolio_byfreelancerJSON', methods: ['GET'])]
    //public function getfreelancer($id, EntityManagerInterface $entityManager): Response
    public function getf(Request $req,$id,PortfolioRepository $repo,  NormalizerInterface $normalizer,ManagerRegistry $doctrine,EntityManagerInterface $entityManager): Response
    
    {

        //$portfolio = $repo->find($idPortfolio);
        $p = $repo->findByFreelancerId($id); 
        $portfolioNormalises = $normalizer->normalize($p, 'json', ['groups' => "PORTFOLIO"]);
        return new Response (json_encode($portfolioNormalises));


        

        //$user = $entityManager->getRepository(User::class)->find(190);
         


       /* $portfolio = $repo->findByFreelancerId($id); 
        return $this->render('portfolio/indexid.html.twig', [
            'portfolioid' => $portfolio,
            
            //'final_budget' => $finalbudget,
        ]);*/
    }



    #[Route('/f/{id}/pdf', name: 'app_portfolio_byfreelancer_pdfJSON', methods: ['GET'])]
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
