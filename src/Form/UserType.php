<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'firstname',
                TextType::class,
                [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Prénom'
                    ]
                ]
            )
            ->add(
                'lastname',
                TextType::class,
                [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nom'
                    ]
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                'label' => false,
                'attr' => [
                    'placeholder' => 'E-mail'
                    ]
                ]
            )
            ->add(
                'address',
                TextType::class,
                [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Adresse'
                    ]
                ]
            )
            ->add(
                'postalCode',
                TextType::class,
                [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Code postal'
                    ]
                ]
            )
            ->add(
                'city',
                TextType::class,
                [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Ville'
                    ]
                ]
            )
            ->add(
                'country',
                CountryType::class,
                [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Pays'
                    ]
                ]
            )
            ->add(
                'phone',
                TelType::class,
                [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Téléphone'
                    ]
                ]
            )
            ->add(
                'stringBirthday',
                TextType::class,
                [
                'label' => false,
                'attr' => [
                    'class' => 'form-inline',
                    'placeholder' => 'Votre date de naissance'
                    ]
                ]
            )
            ->add(
                'password',
                RepeatedType::class,
                [
                'type' => PasswordType::class,
                'invalid_message' => "Confirmation du mot de passe invalide",

                'first_options'  => [
                    'label' => false,
                    'attr' => [
                    'placeholder' => 'Mot de passe'
                    ]
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Confirmation du mot de passe'
                        ]
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
            'data_class' => User::class,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
