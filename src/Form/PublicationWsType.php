<?php

namespace App\Form;

use App\Entity\PublicationWs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
class PublicationWsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class, [
                'attr' => [
                    'class' => 'form-control valid',
                    'placeholder' => 'Enter Post Title',
                ],
            ])
            ->add('content',TextType::class, [
                'attr' => [
                    'class' => 'form-control valid',
                    'placeholder' => 'Enter Post Content',
                ],
            ])
            ->add('author',TextType::class, [
                'attr' => [
                    'class' => 'form-control valid',
                    'placeholder' => 'Enter Post Author',
                ],
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PublicationWs::class,
        ]);
    }
}
