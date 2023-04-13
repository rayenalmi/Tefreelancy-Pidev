<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\NotBlank;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quest', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ quest ne doit pas être vide.',
                    ]),
                ],
                'empty_data' =>'',
            ])
            ->add('choice1', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ choice 1 ne doit pas être vide.',
                    ]),
                ],
                'empty_data' =>'',
            ])
            ->add('choice2', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ choice 2 ne doit pas être vide.',
                    ]),
                ],
                'empty_data' =>'',
            ])
            ->add('choice3', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ choice 3 ne doit pas être vide.',
                    ]),
                ],
                'empty_data' =>'',
            ])
            ->add('response', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ réponse ne doit pas être vide.',
                    ]),
                ],
                'empty_data' =>'',
            ])
            ->add('idTest', IntegerType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ id test ne doit pas être vide.',
                    ]),
                ],
                'empty_data' =>0,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
