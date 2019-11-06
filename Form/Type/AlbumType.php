<?php

namespace MelisPlatformFrameworkSymfonyDemoToolLogic\Form\Type;

use MelisPlatformFrameworkSymfonyDemoToolLogic\Entity\Album;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class AlbumType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('alb_name', TextType::class, [
            'label' => 'tool.album_table_column_name',
            'attr' => [
                'class' => 'form-control'
            ],
            'constraints' => new NotBlank(),
            'required' => true,
        ])
        ->add('alb_song_num', null, [
            'label' => 'tool.album_table_column_song_no',
            'attr' => [
                'class' => 'form-control'
            ],
            'constraints' => [
                new NotBlank(),
                new Positive(['message' => 'tool.song_number_int_only'])
            ],
            'required' => true,
        ]);
    }

    /**
     * Set form default value
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' => Album::class
        ]);
    }

    /**
     * Remove the form type name
     * so that we can use some of the
     * melis javascript helper and tool
     *
     * @return string|null
     */
    public function getBlockPrefix()
    {
        return null;
    }
}