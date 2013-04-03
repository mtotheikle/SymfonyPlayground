<?php

namespace Acme\DemoBundle\Form;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('timestamp')
            ->add('username')
            ->add('Roles', 'entity', array(
                'class'         => 'Acme\\DemoBundle\\Entity\\UserRole',
                'multiple'      => true,
                'expanded'      => true,
                'query_builder' => function(EntityRepository $er) {
                    $qb = $er->createQueryBuilder('ur')
                        ->where('ur.name = :name OR ur.name = :name2');
                    $qb->setParameter('name', 'Test #1');
                    $qb->setParameter('name2', 'Test #2');

                    return $qb;
                },
                //'choice_list'   => new \Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList(array('Test #1' => 'Test'))
            ))
            ->addEventListener(\Symfony\Component\Form\FormEvents::POST_BIND, function(\Symfony\Component\Form\FormEvent $event) {
                $form = $event->getForm();
                $choiceList = $form->get('Roles')->getConfig()->getOption('choice_list');
                $availableChoices = $choiceList->getChoices();

                echo '<pre>';
                \Doctrine\Common\Util\Debug::dump($availableChoices);
                \Doctrine\Common\Util\Debug::dump($form->getData()->getRoles());
                $data = $event->getData();
                //$data['Roles'][] = '3';
                \Doctrine\Common\Util\Debug::dump($data);
                $event->setData($data);
                echo '</pre>';
            });
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acme\DemoBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'acme_demobundle_usertype';
    }
}
