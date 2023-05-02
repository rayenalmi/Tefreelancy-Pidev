<?php

namespace App\Form;

use App\Entity\Workspace;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;

class WorkspaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control valid',
                    'placeholder' => 'Enter Workspace Name',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter a workspace name',
                    ]),
                    new Assert\Type([
                        'type' => 'string',
                        'message' => 'The workspace name must be a string',
                    ])
                ],
                'empty_data' => '',
            ])

            ->add('description', TextType::class, [
                'attr' => [
                    'class' => 'form-control valid',
                    'placeholder' => 'Enter Workspace Description',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter a workspace description',
                    ]),
                ],
                'empty_data' => '',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Workspace::class,
        ]);
    }
}
