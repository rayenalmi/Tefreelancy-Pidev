<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class StartController extends AbstractController
{
    #[Route('/start', name: 'app_start')]
    public function index(): Response
    {
        $session = new Session(); 
        $session->start(); 
        $session->set('id', 52);
        // $session->get('key')
        
        return $this->render('base.html.twig', [
        ]);
    }
}
