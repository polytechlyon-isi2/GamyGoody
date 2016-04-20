<?php

namespace GamyGoody\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text')
            ->add('content', 'textarea')
            ->add('price', 'money')
            ->add('game', 'choice', [
                'choices' => $options['games'],
                'choices_as_values' => true,
                'choice_label' => function($game) {
                    /** @var Category $category */
                    return $game->getTitle();
                },
                'choice_value' => function($game) {
                    if($game)
                        return $game->getId();
                    else
                        return null;
                },
                'placeholder' => 'Select a game'
            ]);

        $builder->add('images', 'collection', array(
            'type'         => new ArticleImageType(),
            'allow_add'    => true,
            'allow_delete' => true,
            ));
        $builder->add('save', 'submit');

    }

    public function getName()
    {
        return 'article';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array("games"));
    }
}