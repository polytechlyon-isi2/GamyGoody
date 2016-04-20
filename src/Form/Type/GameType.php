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
            ->add('logo', new ImageType(), array('label' => 'Logo (image file)'))
            ->add('background', new ImageType(), array('label' => 'Background (image file)'));
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