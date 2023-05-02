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
use Symfony\Component\Validator\Constraints\NotBlank;

class CandidacyType1 extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('object', TextType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Le champ object ne doit pas être vide.',
                ]),
            ],
            'attr' => [
                'class' => 'form-control valid',
                'placeholder' => 'Enter your object',
            ],
        ])

        ->add('message', TextType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Le champ message ne doit pas être vide.',
                ]),
            ],
            'attr' => [
                'class' => 'form-control valid',
                'placeholder' => 'Enter your message',
            ],
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
