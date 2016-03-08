<?php

namespace GamyGoody\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text')
            ->add('logo', 'file', array('label' => 'Logo (image file)',
            	'constraints' => array(new Assert\Image())))
            ->add('background', 'file', array('label' => 'Background (image file)',
            	'constraints' => array(new Assert\Image())));
    } 

    public function getName()
    {
        return 'game';
    }
}
/*[
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF',
                    ]*/