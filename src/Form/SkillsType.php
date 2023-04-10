<?php

namespace App\Form;

use App\Entity\Skills;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;



class SkillsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('name')
            ->add('level')
            ->add('idFreelancer',EntityType::class,[
                'class'=>User::class,
                "choice_label"=>'email',
                'expanded'=>false,
                'multiple'=>false
            ])
            
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Skills::class,
        ]);
    }
}
