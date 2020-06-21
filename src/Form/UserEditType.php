<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('nickname')
            ->add('status')
            ->add('firstname')
            ->add('surname')
            ->add('postcode')
            ->add('city')
            ->add('street')
            ->add('country')
            ->add('phone')
            ->add('gender')
            ->add('emailConfirmed')
            ->add('isSuperadmin')
            ->add('website')
            ->add('steamAccount')
            ->add('hardware')
            ->add('infoMails')
            ->add('statements')
            ->add('birthdate', BirthdayType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'allow_extra_fields' => false,
        ]);
    }
}
