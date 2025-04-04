<?php

namespace App\Controller;

use App\Service\Onboarding\OnboardingManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class OnboardingController extends AbstractController
{
    #[Route('/onboarding', name: 'onboarding')]
    public function index(Request $request, OnboardingManager $onboardingManager)
    {
        $step = $request->query->get('step', 1);

        $form = $onboardingManager->createFormForStep($step);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $onboardingManager->saveStepData($form->getData());

            $nextStep = $onboardingManager->getNextStep($step);
            if ($nextStep !== null) {
                return $this->redirectToRoute('onboarding', ['step' => $nextStep]);
            }

            // it was the last step, we can save data to database:
            $onboardingManager->persistData();

            return $this->redirectToRoute('onboarding_success');
        }

        return $this->render("onboarding/step{$step}.html.twig", [
            'form' => $form->createView(),
            'step' => $step,
        ]);
    }

    #[Route('/onboarding/success', name: 'onboarding_success')]
    public function success()
    {
        return $this->render('onboarding/success.html.twig');
    }
}
