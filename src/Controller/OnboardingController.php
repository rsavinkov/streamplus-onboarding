<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\PaymentDetails;
use App\Entity\User;
use App\Enum\SubscriptionType;
use App\Form\AddressInformationType;
use App\Form\PaymentDetailsType;
use App\Form\UserInformationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class OnboardingController extends AbstractController
{
    #[Route('/onboarding', name: 'onboarding')]
    public function index(Request $request, SessionInterface $session)
    {
        // Initialize session data if not set
        if (!$session->has('onboarding_data')) {
            $session->set('onboarding_data', []);
        }
        $data = $session->get('onboarding_data');

        $step = $request->query->get('step', 1);

        $form = match ((int)$step) {
            1 => $this->createForm(UserInformationType::class),
            2 => $this->createForm(AddressInformationType::class),
            3 => $this->createForm(PaymentDetailsType::class),
            default => throw $this->createNotFoundException('Invalid step'),
        };

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->set('onboarding_data', array_merge($data, $form->getData()));


            if (
                ($step == 2 && $data['subscriptionType'] === SubscriptionType::FREE) // Skip payment step if subscription is free
                || $step == 3
            ) {
                return $this->redirectToRoute('onboarding_submit');
            }

            // Proceed to next step
            return $this->redirectToRoute('onboarding', ['step' => $step + 1]);
        }

        return $this->render("onboarding/step{$step}.html.twig", [
            'form' => $form->createView(),
            'step' => $step,
        ]);
    }

    #[Route('/onboarding/submit', name: 'onboarding_submit')]
    public function submit(SessionInterface $session, EntityManagerInterface $em)
    {
        $data = $session->get('onboarding_data');

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

        if ($data['subscriptionType'] === 'premium') {
            $payment = new PaymentDetails();
            $payment->setCardNumber($data['cardNumber']);
            $payment->setExpirationDate($data['expirationDate']);
            $payment->setCvv($data['cvv']);
            $user->setPayment($payment);
        }

        $em->persist($user);
        $em->flush();

        // Clear session
        $session->remove('onboarding_data');

        return $this->redirectToRoute('onboarding_success');
    }

    #[Route('/onboarding/success', name: 'onboarding_success')]
    public function success()
    {
        return $this->render('onboarding/success.html.twig');
    }
}
