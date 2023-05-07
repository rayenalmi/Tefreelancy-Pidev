<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Entity\User;
use App\Form\OfferType;
use App\Form\OfferTypeEdit;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\SearchOfferType;
use Dompdf\Dompdf;
use Dompdf\Options;
use FontLib\Table\Type\name;
use Symfony\Component\Validator\Constraints\Length;

#[Route('/start/offer')]
class OfferController extends AbstractController

{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getOfferByID(int $id): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT o.idOffer,o.name,o.description,o.duration,o.keywords,o.salary FROM App\Entity\Offer o  WHERE o.idRecruter = :id'
        )->setParameter('id', $id);

        return $query->getResult();
    }
    public function getOfferByName(string $name): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT o FROM App\Entity\Offer o WHERE o.name = :name'
        )->setParameter('name', $name);

        return $query->getResult();
    }


    public function RechercheAsc(): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT o
            FROM App\Entity\Offer o
            ORDER BY o.name ASC'
        );

        return $query->getResult();
    }

    #[Route('/listOffreAsc', name: 'OffreAsc')]
    public function listeOffreAsc(Request $req, EntityManagerInterface $entityManager): Response
    {
        $offers = $this->RechercheAsc();
        $form = $this->createForm(SearchOfferType::class);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {

            /*var_dump($this); 
            die() ;*/
            $offers = $this->findOffer($form->getData('search'));

            return $this->render('offer/index.html.twig', [
                'offers' => $offers,
                'form' => $form->createView()
            ]);
        }

        return $this->render('offer/index.html.twig', [
            'offers' => $offers,
            'form' => $form->createView()
        ]);
    }

    public function RechercheDesc(): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT o
            FROM App\Entity\Offer o
            ORDER BY o.name DESC'
        );

        return $query->getResult();
    }
    #[Route('/listOffreDesc', name: 'OffreDesc')]
    public function listeOffreDesc(Request $req, EntityManagerInterface $entityManager): Response
    {
        $offers = $this->RechercheDesc();
        $form = $this->createForm(SearchOfferType::class);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {

            /*var_dump($this); 
            die() ;*/
            $offers = $this->findOffer($form->getData('search'));

            return $this->render('offer/index.html.twig', [
                'offers' => $offers,
                'form' => $form->createView()
            ]);
        }

        return $this->render('offer/index.html.twig', [
            'offers' => $offers,
            'form' => $form->createView()
        ]);
    }

    public function RechercheSalaryAsc(): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT o
            FROM App\Entity\Offer o
            ORDER BY o.salary ASC'
        );

        return $query->getResult();
    }

    #[Route('/listOffreSalaryAsc', name: 'OffreSalaryAsc')]
    public function listeOffreSalaryAsc(Request $req, EntityManagerInterface $entityManager): Response
    {
        $offers = $this->RechercheSalaryAsc();
        $form = $this->createForm(SearchOfferType::class);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {

            /*var_dump($this); 
            die() ;*/
            $offers = $this->findOffer($form->getData('search'));

            return $this->render('offer/index.html.twig', [
                'offers' => $offers,
                'form' => $form->createView()
            ]);
        }

        return $this->render('offer/index.html.twig', [
            'offers' => $offers,
            'form' => $form->createView()
        ]);
    }

    public function RechercheSalaryDesc(): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT o
            FROM App\Entity\Offer o
            ORDER BY o.salary DESC'
        );

        return $query->getResult();
    }

    #[Route('/listOffreSalaryDesc', name: 'OffreSalaryDesc')]
    public function listeOffreSalaryDesc(Request $req, EntityManagerInterface $entityManager): Response
    {
        $offers = $this->RechercheSalaryDesc();
        $form = $this->createForm(SearchOfferType::class);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {

            /*var_dump($this); 
            die() ;*/
            $offers = $this->findOffer($form->getData('search'));

            return $this->render('offer/index.html.twig', [
                'offers' => $offers,
                'form' => $form->createView()
            ]);
        }

        return $this->render('offer/index.html.twig', [
            'offers' => $offers,
            'form' => $form->createView()
        ]);
    }



    public function findOffer($keyword)
    {
            /* var_dump($keyword); 
        die()*/; //transforme array to string 
        $ch = implode('', $keyword);
        $query = $this->entityManager
            ->createQuery(
                'SELECT o
            FROM App\Entity\Offer o
            WHERE o.keywords LIKE  :keywords'
            )
            ->setParameter('keywords', '%g%');

        return $query->getResult();
    }


    #[Route('/RecruterOffer', name: 'app_offer_index_recruter', methods: ['GET', 'POST'])]
    public function indexRecruter(Request $req, EntityManagerInterface $entityManager): Response
    {
        $session = $req->getSession();
        $u = $session->get('user');
        $urs = $this->entityManager->getRepository(User::class)->find($u->getIdUser());
        $offers = $this->getOfferByID($urs->getIdUser());
        $form = $this->createForm(SearchOfferType::class);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {

            /*var_dump($this); 
                die() ;*/
            $offers = $this->findOffer($form->getData('search'));

            return $this->render('offer/index_recruter.html.twig', [
                'offers' => $offers,
                'form' => $form->createView()
            ]);
        }

        return $this->render('offer/index_recruter.html.twig', [
            'offers' => $offers,
            'form' => $form->createView()
        ]);
    }

    #[Route('/getoffersrecrutermobile', name: 'app_offer_recrutermobile')]
    public function indexMobileRecruter(Request $req, EntityManagerInterface $entityManager)
    {
        $data = json_decode($req->getContent(), true);
        $id = $data['id'];

        $offers = $this->getOfferByID($id);
        $alloffer = [] ;
        foreach ( $offers as $o) {
           $u = [
                'id' => $o['idOffer'],
                'name' =>$o['name'],
                'desc' => $o['description'],
                'duration' => $o['duration'],
                'keyword'=> $o['keywords'],
                'salaire' => $o['salary'],
           ];
           $alloffer [] = $u ;
        }

        $json = json_encode($alloffer);
        
        $response = new JsonResponse($json, 200, [], true);
        return $response ;
    }

    #[Route('/getoffersmobile', name: 'app_offer_getallmobiile')]
    public function indexMobile(Request $req, EntityManagerInterface $entityManager)
    {
        $offers = $entityManager->getRepository(Offer::class)->findAll();
        $alloffer = [] ;
        foreach ( $offers as $o) {
           $u = [
                'id' => $o->getIdOffer(),
                'name' => $o->getName(),
                'desc' => $o->getDescription(),
                'duration' => $o->getDuration(),
                'keyword'=> $o->getKeywords(),
                'salaire' =>$o->getSalary(),
                'idrec'=> $o->getIdRecruter()->getIdUser()
           ];
           $alloffer [] = $u ;
        }

        $json = json_encode($alloffer);
        
        $response = new JsonResponse($json, 200, [], true);
        return $response;
    }

    #[Route('/', name: 'app_offer_index', methods: ['GET', 'POST'])]
    public function index(Request $req, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $offers = $entityManager->getRepository(Offer::class)->findAll();
        $offers = $paginator->paginate(
            $offers,
            $req->query->getInt(key: 'page', default: 1),
            limit: 2
        );
        $form = $this->createForm(SearchOfferType::class);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {

            /*var_dump($this); 
                die() ;*/
            $offers = $this->findOffer($form->getData('search'));

            return $this->render('offer/index.html.twig', [
                'offers' => $offers,
                'form' => $form->createView()
            ]);
        }

        return $this->render('offer/index.html.twig', [
            'offers' => $offers,
            'form' => $form->createView()
        ]);
    }

    #[Route('/rechercheoffre', name: 'recherche_ajax_offre')]
    public function rechercheAjadx(Request $request): JsonResponse
    {
        $requestString = $request->query->get('searchValue');

        $resultats = $this->entityManager
            ->createQuery(
                'SELECT o
            FROM App\Entity\Offer o
            WHERE o.keywords LIKE  :keywords'
            )
            ->setParameter('keywords', '%' . $requestString . '%')
            ->getArrayResult();

        return $this->json($resultats);
    }

    #[Route('/newmobile', name: 'app_offer_mobile', methods: ['GET', 'POST'])]
    public function newmobile(Request $request, EntityManagerInterface $entityManager)
    {
        $data = json_decode($request->getContent(), true);

        $name = $data['name'];
        $desc = $data['desc'];
        $duration = $data['duration']; 
        $key = $data['key']; 
        $sal = $data['sal']; 
        $id = $data['id']; 

        $sal = (float) $sal ;   

        $urs = $this->entityManager->getRepository(User::class)->find($id);

        $o = new Offer();
        $o->setName($name);
        $o->setDescription($desc);
        $o->setDuration($duration);
        $o->setKeywords($key);
        $o->setSalary($sal);
        $o->setIdRecruter($urs);

        $entityManager->persist($o);
        $entityManager->flush();
        return new JsonResponse(['succes' => 'welcome']);
    }

    #[Route('/new', name: 'app_offer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $u = $session->get('user');
        $offer = new Offer();
        $offer->setIdRecruter($u);
        $form = $this->createForm(OfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $x = $this->getOfferByName($form["name"]->getData());
            if (count($x) != 0) {
                $this->addFlash('error', 'alerte!');
                return $this->redirectToRoute('app_offer_new', [], Response::HTTP_SEE_OTHER);
            }
            $entityManager->persist($offer);
            $entityManager->flush();

            return $this->redirectToRoute('app_offer_index_recruter', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('offer/new.html.twig', [
            'offer' => $offer,
            'form' => $form,
        ]);
    }

    #[Route('details/{idOffer}', name: 'app_offer_show1', methods: ['GET'])]
    public function show(Offer $offer): Response
    {
        return $this->render('offer/show.html.twig', [
            'offer' => $offer,
        ]);
    }
    #[Route('/recommended_offer', name: 'app_offer_show', methods: ['GET','POST'])]
    public function shxcbdgow(Request $request): Response
    {
        $session = $request->getSession();
        $u = $session->get('user');
        $urs = $this->entityManager->getRepository(User::class)->find($u->getIdUser());
    
        $query = $this->entityManager->createQuery(
            'SELECT s
            FROM App\Entity\Skills s
            JOIN App\Entity\User u WITH s.idFreelancer = u.idUser
            WHERE s.idFreelancer = :userId'
        )->setParameter('userId', $urs);
    
        $listSk = $query->getResult();

        $list = [];
        $query = $this->entityManager->createQuery(
            'SELECT o
            FROM App\Entity\Offer o
            ORDER BY o.salary DESC'
        );
    $qb = $this->entityManager->createQueryBuilder();
    $qb->select('o')
        ->from(Offer::class, 'o');
    $result = $qb->getQuery()->getResult();
    foreach ($result as $row) {
        $o = new Offer();
        $o = $row;
        $listKey = explode(' ', $row->getKeywords());
        $percent = $this->countPercentLinktoOffer($listSk, $listKey);
        $o->setPercent($percent);
        $list[] = $o;
        

    }
    foreach ($list as $o) {
        var_dump($o->getName());
        var_dump($o->getPercent());
    }

    return $this->renderForm('offer/recommendedOffer.html.twig', [
        'offer' => $list,
        
    ]);
    }



    #[Route('/{idOffer}/edit', name: 'app_offer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offer $offer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OfferTypeEdit::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('offer/edit.html.twig', [
            'offer' => $offer,
            'form' => $form,
        ]);
    }

    #[Route('/{idOffer}', name: 'app_offer_delete', methods: ['POST'])]
    public function delete(Request $request, Offer $offer, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $offer->getIdOffer(), $request->request->get('_token'))) {
            $entityManager->remove($offer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/pdf/{id}', name: 'PDF_offer', methods: ['GET'])]
    public function pdf(EntityManagerInterface $entityManager, $id)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Open Sans');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $offer = $entityManager->getRepository(Offer::class)->find($id);
        $html = $this->renderView('test/pdf.html.twig', [
            'offer' => $offer,
        ]);

        // Add header HTML to $html variable
        $headerHtml = '<h1 style="text-align: center; color: #b0f2b6;">Bienvenue chez Freelancy </h1>';
        $html = $headerHtml . $html;

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        // Send the PDF to the browser
        $response = new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="Offer.pdf"',
        ]);

        return $response;
    }



    #[Route('/dgsdvsdv', name: 'appcxvcvxc_yass')]
    function xcbxcbcxb(): Response
    {
        return new Response('Hello, world!');
    }

    #[Route('/recommended', name: 'offer_recommended', methods: ['GET'])]
    public function recommandedOffre(Request $request)
{
   /* $session = $request->getSession();
    $u = $session->get('user');
    $urs = $this->entityManager->getRepository(User::class)->find($u->getIdUser());

    $query = $this->entityManager->createQuery(
        'SELECT s
        FROM App\Entity\Skills s
        JOIN App\Entity\User u WITH s.idFreelancer = u.idUser
        WHERE s.idFreelancer = :userId'
    )->setParameter('userId', 199);

    $listSk = $query->getResult();

    $list = [];
    $qb = $this->entityManager->createQueryBuilder();
    $qb->select('o.id', 'o.name', 'o.description', 'o.duration', 'o.keywords', 'o.salary', 'o.recruterId')
        ->from(Offer::class, 'o');
    $result = $qb->getQuery()->getResult();
    foreach ($result as $row) {
        $o = new Offer();

        $listKey = explode(' ', $row['keywords']);
        $percent = $this->countPercentLinktoOffer($listSk, $listKey);
        $o->setPercent($percent);
        $list[] = $o;
    }
   // return $list;
    */
   return new Response('Hello, world!');
}

public function countPercentLinktoOffer(array $lsk, array $listKey): float
{
    $nb = 0;
    foreach ($lsk as $skill) {
        if (in_array($skill->getName(), $listKey)) {
            $nb++;
        }
    }
    return (float) $nb / count($listKey);
}


}
