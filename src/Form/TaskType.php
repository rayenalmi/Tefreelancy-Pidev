<?php

namespace App\Form;

use App\Entity\Task;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'form-control valid',
                    'placeholder' => 'Enter task title',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a title',
                    ]),
                ],
                'empty_data' => '',
            ])
            ->add('description', TextType::class, [
                'attr' => [
                    'class' => 'form-control valid',
                    'placeholder' => 'Enter task description',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter a task description',
                    ]),
                ],
                'empty_data' => '',
            ])
            ->add('deadline', DateType::class, [
                'label' => 'Deadline',
                'attr' => [
                    'class' => 'form-control',
                ],
                'data' => new \DateTime(), // set the default value to the current date
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter a valid date',
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'The deadline should be today or later.',
                    ]),
                    
                ],
                'empty_data' => '',
            ])
            

            // ->add('completed', CheckboxType::class, [
            //     'required' => false,
            //     'data' => false, // set the default value to false
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
