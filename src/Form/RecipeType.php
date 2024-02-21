<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Title')
            ->add('slug')
            ->add('content')
            ->add('duration')
            ->add('Save', SubmitType::class, [
                'label' => 'Mise à jour'
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->autoSlug(...))
            ->addEventListener(FormEvents::POST_SUBMIT)
        ;
    }

    public function autoSlug(PreSubmitEvent $event): void
    {
        $data = $event->getData() ;
        if(empty($data['slug'])) {
            $slugger = new AsciiSlugger() ;
            $data['slug'] = strtolower($slugger->slug($data['title'])) ;
            $event->setData($data);
        }
    }

//    public function auto

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
