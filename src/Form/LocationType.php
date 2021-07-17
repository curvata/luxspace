<?php

namespace App\Form;

use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Titre'
                ]
                ]
            )
            ->add(
                'slug',
                TextType::class,
                [
                'label' => 'slug',
                'attr' => [
                    'placeholder' => 'Slug'
                ]
                ]
            )
            ->add(
                'shortDescription',
                TextType::class,
                [
                'label' => 'Description courte',
                'attr' => [
                    'placeholder' => 'Description courte'
                ]
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Description'
                ]
                ]
            )
            ->add(
                'headerPictureFile',
                FileType::class,
                [
                'label' => 'Image d\'en-tête',
                'required' => false
                ]
            )
            ->add(
                'pictureFiles',
                FileType::class,
                [
                'label' => 'Galerie photos',
                'required' => false,
                'multiple' => true
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
            'data_class' => Location::class
            ]
        );
    }
}
