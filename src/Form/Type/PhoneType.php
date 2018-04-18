<?php
/**
 * Created by PhpStorm.
 * User: ulrich
 * Date: 08/04/2018
 * Time: 06:30
 */

namespace App\Form\Type;

use App\Entity\Phone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mark', TextType::class)
            ->add('reference', TextType::class)
            ->add('description', TextType::class)
            ->add('price', MoneyType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => Phone::class,
            'csrf_protection' => false
            )
        );
    }
}
