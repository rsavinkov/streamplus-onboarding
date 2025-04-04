<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class AddressInformationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('line1', TextType::class, [
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('line2', TextType::class, [
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('postalCode', TextType::class, [
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('state', TextType::class, [
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('country', CountryType::class, [
                'constraints' => [new Assert\NotBlank()],
            ])
        ;
    }
}
