<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\PaymentDetails;
use App\Enum\SubscriptionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserInformationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [new Assert\NotBlank(), new Assert\Length(min: 2, max: 100)],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [new Assert\NotBlank(), new Assert\Email()],
            ])
            ->add('phone', TelType::class, [
                'constraints' => [new Assert\NotBlank(), new Assert\Length(min: 10)],
            ])
            ->add('subscriptionType', EnumType::class, [
                'class' => SubscriptionType::class,
                'expanded' => true,
                'constraints' => [new Assert\NotBlank()],
            ])
        ;
    }
}
