<?php

namespace App\Form;

use App\Entity\Chapters;
use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\{TextType,ButtonType,EmailType,HiddenType,PasswordType,TextareaType,SubmitType,NumberType,DateType,MoneyType,BirthdayType};
use Symfony\Component\Validator\Constraints\NotBlank;



class ChaptersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [         // add default attributes
                    'placeholder' => 'Enter Name Chapter',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter  a Name',]),
                ],
            ])
            ->add('context' ,TextType::class, [
                'attr' => [         // add default attributes
                    'placeholder' => 'Enter Name context',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a context',]),
                ],
            ])
            ->add('formation',EntityType::class,[
                'class'=>Formation::class,
                "choice_label"=>'name',
                'expanded'=>false,
                'multiple'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chapters::class,
        ]);
    }
}
