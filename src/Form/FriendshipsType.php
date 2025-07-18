<?php

namespace App\Form;

use App\Entity\Friendships;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FriendshipsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('receiver', EntityType::class, [
                'class' => Users::class,
                'choice_label' => 'username',
            ])
            ->add('sender', EntityType::class, [
                'class' => Users::class,
                'choice_label' => 'username',
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Friendships::class,
        ]);
    }
}
