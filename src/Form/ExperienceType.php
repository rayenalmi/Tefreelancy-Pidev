<?php

namespace App\Form;

use App\Entity\Experience;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ExperienceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('title')
            ->add('description')
            ->add('location')
            ->add('duration')
            
            ->add('type', ChoiceType::class, [
                'choices'  => [
                    'education' => 'education',
                    'training' => 'training',
                    'qualification' => 'qualification',
                ],
            ])
            
            ->add('idFreelancer', EntityType::class,[
                'class'=>User::class,
                "choice_label"=>'id_user',
                'expanded'=>false,
                'multiple'=>false
            ])
            ->add('idFreelancer')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Experience::class,
        ]);
    }
}
