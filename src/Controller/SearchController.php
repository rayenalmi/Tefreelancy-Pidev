<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/search')]
class SearchController extends AbstractController{


// search & filter 

    #[Route('/1', name: 'search_skills', methods: ['GET'])]
    public function searchSkills(Request $request)
    {

        //$searchSkillsForm = $this->createForm(SearchSkillType::class);


        return $this->render('search/skills.html.twig', [
            '//search_form' => $searchSkillsForm->createView(),
        ]);
    }
}