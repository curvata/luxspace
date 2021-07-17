<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'firstname',
                TextType::class,
                [
                'label' => false,
                'constraints' => [
                    new NotBlank(),
                    new Length(
                        null,
                        3,
                        20,
                        null,
                        null,
                        null,
                        "Le prénom doit comporter au moins {{ limit }} caractères",
                        "Le prénom ne peut pas comporter plus de {{ limit }} caractères"
                    )
                ],
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
                'constraints' => [
                    new NotBlank(),
                    new Length(
                        null,
                        5,
                        30,
                        null,
                        null,
                        null,
                        "Le nom doit comporter au moins {{ limit }} caractères",
                        "Le nom ne peut pas comporter plus de {{ limit }} caractères"
                    )
                ],
                'attr' => [
                    'placeholder' => 'Nom'
                    ]
                ]
            )
            ->add(
                'address',
                TextType::class,
                [
                'label' => false,
                'constraints' => [
                    new NotBlank(),
                    new Length(
                        null,
                        5,
                        80,
                        null,
                        null,
                        null,
                        "L'adresse doit comporter au moins {{ limit }} caractères",
                        "L'adresse ne peut pas comporter plus de {{ limit }} caractères"
                    )
                ],
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
                'constraints' => [
                    new NotBlank(),
                    new Length(
                        null,
                        3,
                        10,
                        null,
                        null,
                        null,
                        "Le code postal doit comporter au moins {{ limit }} caractères",
                        "Le code postal ne peut pas comporter plus de {{ limit }} caractères"
                    )
                ],
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
                'constraints' => [
                    new NotBlank(),
                    new Length(
                        null,
                        5,
                        20,
                        null,
                        null,
                        null,
                        "La ville doit comporter au moins {{ limit }} caractères",
                        "La ville ne peut pas comporter plus de {{ limit }} caractères"
                    )
                ],
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
                'constraints' => [
                    new NotBlank(),
                    new Length(
                        null,
                        2,
                        40,
                        null,
                        null,
                        null,
                        "Le pays doit comporter au moins {{ limit }} caractères",
                        "Le pays ne peut pas comporter plus de {{ limit }} caractères"
                    )
                ],
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
                'constraints' => [
                    new NotBlank(),
                    new Length(
                        null,
                        8,
                        30,
                        null,
                        null,
                        null,
                        "Le numéro de téléphone doit comporter au moins {{ limit }} caractères",
                        "Le numéro de téléphone ne peut pas comporter plus de {{ limit }} caractères"
                    )
                ],
                'attr' => [
                    'placeholder' => 'Téléphone'
                    ]
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => false,
                    'constraints' => [
                        new NotBlank(null, "Merci de renseigner une adresse e-mail"),
                        new Email(null, "L'e-mail {{ value }} n'est pas valide."),
                        new Length(
                            null,
                            6,
                            50,
                            null,
                            null,
                            null,
                            "L'e-mail doit comporter au moins {{ limit }} caractères",
                            "L'e-mail ne peut pas comporter plus de {{ limit }} caractères"
                        )
                    ],
                    'attr' => [
                        'placeholder' => 'E-mail'
                        ]
                    ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
            ]
        );
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
