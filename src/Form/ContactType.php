<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'firstname',
                TextType::class,
                [
                'label' => false,
                'required' => true,
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
                'required' => true,
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
                'required' => true,
                'attr' => [
                    'placeholder' => 'E-mail'
                ]
                ]
            )
            ->add(
                'message',
                TextType::class,
                [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Votre message'
                ]
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn_secondary full'
                ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
