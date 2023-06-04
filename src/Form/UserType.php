<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            // ->add('roles')
            ->add('password')
            // ->add('isValidated')
            ->add('name')
            ->add('firstname')
            // ->add('registrationDate')
            // ->add('token')
            ->add('avatar', FileType::class, [
                'attr'=>[
                    'label'=> 'Avatar: ', 
                    'placeholder'=>'Add one avatar to your profile'
                ],
                'by_reference' => false,
                'required'=>false,
                'mapped'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
