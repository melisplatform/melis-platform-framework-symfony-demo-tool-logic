<?php

namespace MelisPlatformFrameworkSymfonyDemoToolLogic\Form\Type;

use MelisPlatformFrameworkSymfonyDemoToolLogic\Entity\Album;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlbumType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('alb_name', null, [
            'label' => 'Name',
            'attr' => [
                'class' => 'form-control'
            ]
        ]);
        $builder->add('alb_song_num', null, [
            'label' => 'Song number',
            'attr' => [
                'class' => 'form-control'
            ]
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' => Album::class
        ]);
    }
}