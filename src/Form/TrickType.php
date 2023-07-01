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
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

class TrickType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    } 

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description', TextareaType::class, [
                'label' => $this->translator->trans('Description'),
                'attr'=>[
                    'class'=> 'form-control'
                ],
                'required'=> true
            ])
            ->add('category', EntityType::class, [
                'class'=>Category::class,
                'choice_label'=> $this->translator->trans('title'),
                'multiple'=>false,
                'required'=>true,
                'label'=> $this->translator->trans('Category'),
                'placeholder'=> $this->translator->trans('Choose_category')
            ])
            ->add('pictures', CollectionType::class, [
                'entry_type' => PictureType::class,
                'attr'=>[
                    'label'=> $this->translator->trans('Picture'), 
                    'placeholder'=> $this->translator->trans('Add one or more media to your trick')
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required'=>true,
                'constraints' => [
                    new Count(min:1, minMessage: $this->translator->trans('min_picture'), groups: ["new", "edit"])
                ]
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => VideoType::class,
                'attr'=>[
                    'label'=> 'Video: ', 
                    'placeholder'=> $this->translator->trans('Add one or more media to your trick')
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required'=>true,
                'constraints' => [
                    new Count(min:1, minMessage: $this->translator->trans('min_video'), groups: ["new", "edit"])
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
