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
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

class NewPwdType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    } 

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type'=> PasswordType::class,
                'required' => true,
                'invalid_message' => $this->translator->trans('Passwords are not the same'),
                'first_options' => [
                    'label' => $this->translator->trans('New password'),
                    'attr'=>[
                        'class' => 'mb-5 d-flex flex-column'
                    ]
                ],
                'second_options' => [
                    'label' => $this->translator->trans('Repeat password'),
                    'attr'=>[
                        'class' => 'mb-5 d-flex flex-column'
                    ]
                ],
                'constraints' => [
                    new NotBlank(['message' => $this->translator->trans('Please enter a new password')]),
                    new Length(['min' => 6, 'minMessage' => $this->translator->trans('min_message', ['%limit%' => 6])])
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
