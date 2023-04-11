<?php

namespace App\Form;

use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{TextType,ButtonType,EmailType,HiddenType,PasswordType,TextareaType,SubmitType,NumberType,DateType,MoneyType,BirthdayType};
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [         // add default attributes
                    'placeholder' => 'Enter Name ',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a Name',]),
                ],
            ])
            ->add('nbh', IntegerType::class, [
                'label' => 'Number Of Hours',
                'attr' => [         // add default attributes
                    'placeholder' => 'Enter Number Of Hours',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a number',
                    ]),
                    new Type([
                        'type' => 'numeric',
                        'message' => 'Please enter a valid number',
                    ]),
                ],
            ])
            ->add('nbl', IntegerType::class, [
                'label' => 'Number Of Lesson',
                'attr' => [         // add default attributes
                    'placeholder' => 'Enter Number Of Lesson',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a number',
                    ]),
                    new Type([
                        'type' => 'numeric',
                        'message' => 'Please enter a valid number',
                    ]),
                ],
            ])
            ->add('path', TextType::class, [
                'attr' => [         // add default attributes
                    'placeholder' => 'Enter Name Path',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a Name',]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
