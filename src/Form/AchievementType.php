<?php

namespace App\Form;

use App\Entity\Achievement;
use App\Entity\Offer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


use App\Entity\User;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AchievementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
        ->add('idFreelancer',EntityType::class,[
            'class'=>User::class,
            "choice_label"=>'email',
            'expanded'=>false,
            'multiple'=>false
        ])

        ->add('idFreelancer')
        ->add('idOffer', EntityType::class,[
            'class'=>Offer::class,
            "choice_label"=>'name',
            'expanded'=>false,
            'multiple'=>false
        ])
        ->add('idOffer')
            ->add('rate')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Achievement::class,
        ]);
    }
}
