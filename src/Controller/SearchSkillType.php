<?php

namespace App\Controller; 

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Skills;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface; 
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class SearchSkillType extends AbstractController{


    //#[Route('/skills/search', name: 'search_skills', methods: ['GET'])]
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
        
            
            ->add('level', ChoiceType::class, [
                'choices'  => [
                    'Fundamental_Awareness' => 'Fundamental_Awareness',
                    'Novice' => 'Novice',
                    'Intermediate' => 'Intermediate',
                    'Advanced' => 'Advanced',
                    'Expert' => 'Expert' 
                ],
            ])

            ->add('Search', SubmitType::class)

            ;
    }



}