<?php

namespace GamyGoody\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserRegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('role');
    }

    public function getName()
    {
        return 'user_register';
    }

    public function getParent()
    {
        return new UserType();
    }
}