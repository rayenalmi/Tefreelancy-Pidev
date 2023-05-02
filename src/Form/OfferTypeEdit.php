<?php

namespace App\Form;

use App\Entity\Offer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\NotBlank;


class OfferTypeEdit extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Le champ Nom ne doit pas être vide.',
                ]),
            ],
            'attr' => [
                'class' => 'form-control valid',
                'placeholder' => 'Enter your name',
            ],
        ])
        ->add('description', TextType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Le champ description ne doit pas être vide.',
                ]),
            ],
            'attr' => [
                'class' => 'form-control valid',
                'placeholder' => 'Enter your description',
            ],
        ])
        ->add('duration', TextType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Le champ duration ne doit pas être vide.',
                ]),
            ],
            'attr' => [
                'class' => 'form-control valid',
                'placeholder' => 'Enter your duration',
            ],
        ])

        ->add('keywords', TextType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Le champ keywords ne doit pas être vide.',
                ]),
            ],
            'attr' => [
                'class' => 'form-control valid',
                'placeholder' => 'Enter your keywords',
            ],
        ])
            
            
            ->add('salary', NumberType::class, [
                'scale' => 2, // allow 2 decimal places
                'attr' => [
                    'step' => '0.01', // set the input step to 0.01
                    'class' => 'form-control valid',
                    'placeholder' => 'Enter your salary',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ salary ne doit pas être vide.',
                    ]),
                ],
            ])
           
            
            //->add('idUser')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offer::class,
        ]);
    }
}
