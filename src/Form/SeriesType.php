<?php

namespace App\Form;
use App\Entity\Series;
use App\Entity\Categories;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank; 
use Symfony\Bridge\Doctrine\Form\Type\EntityType; 
use Symfony\Component\Form\Extension\Core\Type\FileType;


class SeriesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('resume')
            ->add('directeur')
            ->add('pays')
            ->add('image', FileType::class, [
                'label' => ' Image',
                'mapped' => false,
                'required' => false,
            ])
            ->add('idcategorie', EntityType::class, [
                'class' => Categories::class, 
                'choice_label' => 'nom', 
                'placeholder' => 'Sélectionnez une catégorie', 
                'constraints' =>[
                    new NotBlank(),
                ],
            ]);


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Series::class,
        ]);
    }
}

