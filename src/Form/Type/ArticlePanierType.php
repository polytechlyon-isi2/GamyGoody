<?php

namespace GamyGoody\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface; 
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticlePanierType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('quantity', "integer", ['data' => 1]);
    $builder->add('article', 'hidden');
    $builder->add('Ajouter au panier', 'submit');
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'GamyGoody\Domain\ArticlePanier'
      ));
  }

  public function getName()
  {
    return 'article_panier';
  }
}