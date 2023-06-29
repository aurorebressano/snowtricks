<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class NewPwdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type'=> PasswordType::class,
                'required' => true,
                'invalid_message' => 'Les mots de passe ne sont pas identiques',
                'first_options' => [
                    'label' => 'Nouveau mot de passe: ',
                    'attr'=>[
                        'class' => 'mb-5 d-flex flex-column'
                    ]
                ],
                'second_options' => [
                    'label' => 'Répétez le mot de passe: ',
                    'attr'=>[
                        'class' => 'mb-5 d-flex flex-column'
                    ]
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un mot de passe']),
                    new Length(['min' => 6, 'minMessage' => 'Veuillez saisir au moins {{ limit }} caractères'])
                ] 
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
