<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Category;
use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('category', EntityType::class, [
                'class'=>Category::class,
                'choice_label'=>'title',
                'multiple'=>false,
                'required'=>true,
                'label'=>'Catégorie',
                'placeholder'=>'Choisissez une catégorie de trick'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
