<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PaymentDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cardNumber', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Luhn(), // Validates credit card number
                ],
            ])
            ->add('expirationDate', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex('/^(0[1-9]|1[0-2])\/?([0-9]{2})$/'),
                ],
            ])
            ->add('cvv', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(exactly: 3),
                    new Assert\Type('numeric'),
                ],
            ])
        ;
    }
}
