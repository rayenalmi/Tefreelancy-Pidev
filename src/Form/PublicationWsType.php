<?php

namespace App\Form;

use App\Entity\PublicationWs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Validator\Constraints as Assert;

class PublicationWsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'form-control valid',
                    'placeholder' => 'Enter Post Title',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter a post title',
                    ])
                ],
                'empty_data' => '',
            ])
            ->add('content', TextType::class, [
                'attr' => [
                    'class' => 'form-control valid',
                    'placeholder' => 'Enter Post Content',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter a post content',
                    ])
                ],
                'empty_data' => '',
            ])

            ->add('author', TextType::class, [
                'attr' => [
                    'class' => 'form-control valid',
                    'placeholder' => 'Enter Post Author',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter the post author name',
                    ]),
                ],
                'empty_data' => '',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PublicationWs::class,
        ]);
    }
}
