<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Category;
use App\Entity\Message;
use App\Entity\Picture;
use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Count;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description', TextareaType::class, [
                'label' => 'Description: ',
                'attr'=>[
                    'class'=> 'form-control'
                ],
                'required'=> true
            ])
            ->add('category', EntityType::class, [
                'class'=>Category::class,
                'choice_label'=>'title',
                'multiple'=>false,
                'required'=>true,
                'label'=>'Catégorie',
                'placeholder'=>'Choisissez une catégorie de trick'
            ])
            ->add('pictures', CollectionType::class, [
                'entry_type' => PictureType::class,
                'attr'=>[
                    'label'=> 'Picture: ', 
                    'placeholder'=>'Add one or more media to your trick'
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required'=>true,
                'constraints' => [
                    new Count(min:1, minMessage:'Ajoutez au moins une image', groups: ["new", "edit"])
                ]
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => VideoType::class,
                'attr'=>[
                    'label'=> 'Video: ', 
                    'placeholder'=>'Add one or more media to your trick'
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required'=>true,
                'constraints' => [
                    new Count(min:1, minMessage:'Ajoutez au moins une vidéo', groups: ["new", "edit"])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
            "validation_groups" => []
        ]);
    }
}
