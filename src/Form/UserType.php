<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\Extension\Core\Type\{TextType,ButtonType,EmailType,HiddenType,PasswordType,TextareaType,SubmitType,NumberType,DateType,MoneyType,BirthdayType};
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                'attr' => [         // add default attributes
                    'placeholder' => 'Enter your Last Name',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a Last NAme',]),
                ],
            ])
            ->add('firstName', TextType::class, [
                'attr' => [         // add default attributes
                    'placeholder' => 'Enter your Fisrt Name',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a Fisrt Name',]),
                ],
            ])
            ->add('phone', IntegerType::class, [
                'label' => 'Phone Number',
                'attr' => [         // add default attributes
                    'placeholder' => 'Enter your Number',
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
            ->add('email', EmailType::class, [
                'attr' => [         // add default attributes
                    'placeholder' => 'Enter your Email',
                ],
                'label' => 'Email address',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your email address',
                    ]),
                    new Email([
                        'message' => 'Please enter a valid email address',
                    ]),
                ],
            ])
            ->add('photo', FileType::class, [
                'label' => 'Brochure (PDF file)',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/*',
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Le fichier n\'est pas valide, assurez vous d\'avoir un fichier au format PDF, PNG, JPG, JPEG)',
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'attr' => [         // add default attributes
                    'placeholder' => 'Enter your Password',
                ],
                'label' => 'Password',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()\-_=+{}\[\]\\|:;"\'<>,.\?\/]).{8,}$/',
                        'message' => 'Please enter a valid password (minimum 8 characters, containing at least one uppercase letter, one lowercase letter, one number and one special character)',
                    ]),
                ],
            ]);
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
