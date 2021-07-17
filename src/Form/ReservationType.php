<?php

namespace App\Form;

use App\Entity\Departure;
use App\Entity\Reservation;
use App\Entity\Returned;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'reference',
                TextType::class,
                [
                'label' => 'Référence de la réservation',
                'attr' => [
                    'placeholder' => 'Référence'
                ]
                ]
            )
            ->add(
                'departureprice',
                IntegerType::class,
                [
                'label' => 'Prix vol aller',
                'attr' => [
                    'placeholder' => 'Prix vol aller'
                ]
                ]
            )
            ->add(
                'returnprice',
                IntegerType::class,
                [
                'label' => 'Prix vol retour',
                'attr' => [
                    'placeholder' => 'Prix vol retour'
                ]
                ]
            )
            ->add(
                'client',
                EntityType::class,
                [
                'label' => 'Nom du client',
                'class' => User::class
                ]
            )
            ->add(
                'departure',
                EntityType::class,
                [
                'label' => 'Vol de départ',
                'class' => Departure::class
                ]
            )
            ->add(
                'returned',
                EntityType::class,
                [
                'label' => 'Vol de retour',
                'class' => Returned::class
                ]
            )
            ->add(
                'status',
                ChoiceType::class,
                [
                'label' => 'Statut',
                'choices' => [
                    'Payé' => 1,
                    'Remboursé' => 2
                ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
            'data_class' => Reservation::class,
            ]
        );
    }
}
