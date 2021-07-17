<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Returned;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReturnedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'reference',
                TextType::class,
                [
                'label' => 'Référence',
                'attr' => [
                    'placeholder' => 'Référence'
                ]
                ]
            )
            ->add(
                'date',
                DateTimeType::class,
                [
                'label' => 'Date de départ',
                'attr' => [
                    'placeholder' => 'Date de départ'
                    ]
                ]
            )
            ->add(
                'seat',
                IntegerType::class,
                [
                'label' => 'Nombre de sièges',
                'attr' => [
                    'placeholder' => 'Nombre de sièges'
                ]
                ]
            )
            ->add(
                'rocket',
                TextType::class,
                [
                'label' => 'Nom de la fusée',
                'attr' => [
                    'placeholder' => 'Nom de la fusée'
                ]
                ]
            )
            ->add(
                'duration',
                TextType::class,
                [
                'label' => 'Durée du vol',
                'attr' => [
                    'placeholder' => 'Durée du vol'
                ]
                ]
            )
            ->add(
                'price',
                IntegerType::class,
                [
                'label' => 'Prix par personne',
                'attr' => [
                    'placeholder' => 'Prix par personne'
                ]
                ]
            )
            ->add(
                'ffrom',
                Entitytype::class,
                [
                'label' => 'Départ de',
                'class' => Location::class,
                'attr' => [
                    'placeholder' => 'Départ de'
                ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
            'data_class' => Returned::class,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
