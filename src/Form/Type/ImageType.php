<?php

namespace GamyGoody\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('file', 'file', array('required' => false, 'label' => false,'constraints' => array(new Assert\Image())));
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'GamyGoody\Domain\Image',
      ));
  }

  public function getName()
  {
    return 'image';
  }
}