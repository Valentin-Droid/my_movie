<?php

namespace App\Form;

use App\Entity\Movie;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' =>'Nom'
            ])
            ->add('dateRelease', DateType::class, [
                'label' =>'Date de sortie'
            ])
            ->add('type', TextType::class, [
                'label' =>'Type'
            ] )
            ->add('synopsis', TextType::class, [
                'label' =>'Synopsis'
            ])
            ->add('realisator', TextType::class, [
                'label' =>'Réalisateur'
            ])
            ->add('file', FileType::class, [
                'label'     => 'Affiche du film',
                'mapped'    => false])
            ->add('envoyer',SubmitType::class, [
                'label' => 'Ajouter',
                'attr' => [
                    'class' => 'btn w-100'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
