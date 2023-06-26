<?php

namespace App\Form;

use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                "required"=>false,
                "label" => false,
                'error_bubbling' => true,
                "constraints" => [
                    new NotNull(message: "L'image ne doit pas être vide" ,
                    groups: [
                        "new"
                    ])
                ],
                "attr"=> [
                    "class" => "pictureFile"
                ]
            ])
            ->add('header', CheckboxType::class, [
                "required"=>false,
                "label" => "Image d'en-tête?",
                "attr"=> [
                    "class" => "RadioType"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
            'validation_groups' => []
        ]);
    }
}
