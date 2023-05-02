<?php

namespace App\Form;

use App\Entity\PropertySearchSkill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType; 

class PropertySearchSkillType extends AbstractType
{
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

            ->add('submit', SubmitType::class, [
                'label' => 'Search'
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PropertySearchSkill::class,
            'method' => 'get',
            'csrf_protection'=> false, 
        ]);
    }
}
