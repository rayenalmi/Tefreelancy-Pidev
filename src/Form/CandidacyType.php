<?php

namespace App\Form;

use App\Entity\Candidacy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;
use App\Entity\Offer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CandidacyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('object',TextType::class, [
                'attr' => [
                    'class' => 'form-control valid',
                    'placeholder' => 'Enter your object',
                ],
            ])
            ->add('message',TextType::class, [
                'attr' => [
                    'class' => 'form-control valid',
                    'placeholder' => 'Enter your message',
                ],
            ])
            ->add('idFreelancer',EntityType::class,[
                'class'=>User::class,
                "choice_label"=>'email',
                'expanded'=>false,
                'multiple'=>false
            ])
            ->add('idOffer',EntityType::class,[
                'class'=>Offer::class,
                "choice_label"=>'name',
                'expanded'=>false,
                'multiple'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidacy::class,
        ]);
    }
}
