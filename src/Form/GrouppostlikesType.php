<?php

namespace App\Form;

use App\Entity\Grouppostlikes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GrouppostlikesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idgrouppost')
            ->add('iduser')
            ->add('idgroup')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Grouppostlikes::class,
        ]);
    }
}
