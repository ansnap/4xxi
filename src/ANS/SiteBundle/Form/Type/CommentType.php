<?php

namespace ANS\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * CommentType
 */
class CommentType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('text', 'textarea', array(
                    'label' => 'Текст',
                ))
                ->add('edit', 'submit', array(
                    'label' => 'Отправить',
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ANS\SiteBundle\Entity\Comment',
        ));
    }

    public function getName()
    {
        return 'comment';
    }

}
