<?php

namespace ANS\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * ProfileType
 */
class ProfileType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
                    'label' => 'Ваше имя',
                ))
                ->add('edit', 'submit', array(
                    'label' => 'Изменить',
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ANS\SiteBundle\Entity\User',
        ));
    }

    public function getName()
    {
        return 'profile';
    }

}
