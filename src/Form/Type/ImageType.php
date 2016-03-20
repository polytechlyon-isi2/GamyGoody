<?php

namespace GamyGoody\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('image', 'file', array('constraints' => array(new Assert\Image())));
    }

    public function getName()
    {
        return 'image';
    }
}