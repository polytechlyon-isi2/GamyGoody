<?php

namespace GamyGoody\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticleImageType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
     $builder->add('image', new ImageType())
      ->add('level', 'integer');
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'GamyGoody\Domain\ArticleImage'
      ));
  }

  public function getName()
  {
    return 'article_image';
  }
}