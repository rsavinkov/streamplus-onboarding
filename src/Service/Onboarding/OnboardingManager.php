<?php

declare(strict_types=1);

namespace App\Service\Onboarding;

use App\Entity\Address;
use App\Entity\PaymentDetails;
use App\Entity\User;
use App\Enum\SubscriptionType;
use App\Form\AddressInformationType;
use App\Form\PaymentDetailsType;
use App\Form\UserInformationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class OnboardingManager
{
    public function __construct(
        private readonly DataStorage $dataStorage,
        private readonly FormFactoryInterface $formFactory,
        private readonly EntityManagerInterface $em
    ) {
    }


    public function createFormForStep(int $step): FormInterface
    {
        $formClass = match ($step) {
            1 => UserInformationType::class,
            2 => AddressInformationType::class,
            3 => PaymentDetailsType::class,
            default => throw new \InvalidArgumentException('Invalid step number'),
        };

        $data = $this->getDataForStep($step);

        return $this->formFactory->create($formClass, $data);
    }

    public function saveStepData(array $data): void
    {
        $this->dataStorage->saveData($data);
    }

    public function getNextStep($step): ?int
    {
        $subscriptionType = $this->dataStorage->getData('subscriptionType');
        if (
            ($step == 2 && $subscriptionType === SubscriptionType::FREE) // Skip payment step if subscription is free
            || $step == 3
        ) {
            return null;
        }

        // Proceed to next step
        return $step + 1;
    }

    private function getDataForStep(int $step): array
    {
        $allData = $this->dataStorage->getAllData();

        return match ($step) {
            1 => [
                'name' => $allData['name'] ?? '',
                'email' => $allData['email'] ?? '',
                'phone' => $allData['phone'] ?? '',
                'subscriptionType' => $allData['subscriptionType'] ?? null,
            ],
            2 => [
                'line1' => $allData['line1'] ?? '',
                'line2' => $allData['line2'] ?? '',
                'city' => $allData['city'] ?? '',
                'postalCode' => $allData['postalCode'] ?? '',
                'state' => $allData['state'] ?? '',
                'country' => $allData['country'] ?? '',
            ],
            3 => [
                'cardNumber' => $allData['cardNumber'] ?? '',
                'expirationDate' => $allData['expirationDate'] ?? '',
                'cvv' => $allData['cvv'] ?? '',
            ],
            default => [],
        };
    }

    public function persistData(): void
    {
        $data = $this->dataStorage->getAllData();

        // Create and persist User, Address, Payment entities
        $user = new User();
        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setPhone($data['phone']);
        $user->setSubscriptionType($data['subscriptionType']);

        $address = new Address();
        $address->setLine1($data['line1']);
        $address->setLine1($data['line1']);
        $address->setCity($data['city']);
        $address->setPostalCode($data['postalCode']);
        $address->setState($data['state']);
        $address->setCountry($data['country']);
        $user->setAddress($address);

        if ($data['subscriptionType'] === SubscriptionType::PREMIUM) {
            $payment = new PaymentDetails();
            $payment->setCardNumber($data['cardNumber']);
            $payment->setExpirationDate($data['expirationDate']);
            $payment->setCvv($data['cvv']);
            $user->setPayment($payment);
        }

        $this->em->persist($user);
        $this->em->flush();

        $this->dataStorage->clearData();
    }
}