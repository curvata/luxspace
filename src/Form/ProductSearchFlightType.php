<?php

namespace App\Form;

use App\Entity\Location;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSearchFlightType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'destination',
                EntityType::class,
                [
                'label' => false,
                'class' => Location::class,
                'attr' => [
                    'class' => 'hidden'
                ],
                'required' => true
                ]
            )
            ->add(
                'departure',
                TextType::class,
                [
                'label' => 'Départ',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Date de départ
                ']
                ]
            )
            ->add(
                'returned',
                TextType::class,
                [
                'label' => 'Retour',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Date de retour
                ']
                ]
            )
            ->add(
                'passagers',
                ChoiceType::class,
                [
                'label' => 'Passagers',
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                    '6' => 6,
                    '7' => 7,
                    '8' => 8,
                    '9' => 9
                ],
                'required' => true
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
            'method' => 'get',
            'csrf_protection' => false
            ]
        );
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
