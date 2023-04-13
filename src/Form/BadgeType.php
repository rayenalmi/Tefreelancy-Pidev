<?php

namespace App\Form;

use App\Entity\Badge;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity; 

class BadgeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ name ne doit pas être vide.',
                    ]),
                ],
                'empty_data' =>'',
            ])
            ->add('type', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ type ne doit pas être vide.',
                    ]),
                ],
                'empty_data' =>'',
            ])
            ->add('image', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ image ne doit pas être vide.',
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
            'data_class' => Badge::class,
        ]);
    }
}
