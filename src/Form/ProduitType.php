<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\CategorieProduit;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;





class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'constraints' => [
                    new NotBlank(),
                ],

                'label'=> 'Name',
            ])
            ->add('prix', null, [
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'integer']),
                    
                ],
                'label'=> 'Price',
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 10]),
                ],

                'label'=> 'Description',
            ])
            ->add('quantitep', null, [
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'integer']),
                ],

                'label'=> 'Qantity',
            ])
            ->add('idCategorieproduit', EntityType::class, [
                'class' => CategorieProduit::class,
                'choice_label' => 'nomcategorie',
                'placeholder' => 'Choisir une catégorie',
                'constraints' => [
                    new NotBlank(),
                ],

                'label'=> 'Category',
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,

                'label'=> 'Picture',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
