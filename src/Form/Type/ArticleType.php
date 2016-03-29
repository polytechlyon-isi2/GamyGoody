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
            ->add('game', 'choice', array(
            'choices' => $options['games']))
            ->add('category', 'choice', array(
            'choices' => $options['categories']))
            ->add('image', new ImageType());

        $builder->add('images', 'collection', array(
            'type'         => new ArticleImageType(),
            'allow_add'    => true,
            'allow_delete' => true,
            ));
    }

    public function getName()
    {
        return 'article';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array("games","categories"));
    }
}