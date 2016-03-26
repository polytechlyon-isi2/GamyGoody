<?php

namespace GamyGoody\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use GamyGoody\Domain\User;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $options["app"];

        $builder
            
        ->add('surname', 'text')
        ->add('firstName', 'text')
        ->add('address', 'text')
        ->add('city', 'text')
        ->add('username', 'text', array(
            'constraints' => array(
                new Assert\Callback(function ($data, ExecutionContextInterface $context) use ($app)
                {
                    $this->isUsernameValid($data, $context, $app);
                })
            )
        ))
        ->add('mail', 'text')
        ->add('password', 'repeated', array(
            'type'            => 'password',
            'invalid_message' => 'The password fields must match.',
            'options'         => array('required' => true),
            'first_options'   => array('label' => 'Password'),
            'second_options'  => array('label' => 'Repeat password'),
            ))
        ->add('role', 'choice', array(
            'choices' => array('ROLE_ADMIN' => 'Admin', 'ROLE_USER' => 'User')
            ));
    }

    public function getName()
    {
        return 'user';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array("app"));
    }

    public function isUsernameValid ($data, ExecutionContextInterface $context, $app) 
    {
        if($app["dao.user"]->isUsernameUsed($data))
        {
            $context->addViolation("Username already used.");
        }
    }
}