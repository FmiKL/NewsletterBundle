<?php

namespace App\Bundle\NewsletterBundle\Controller;

use App\Bundle\NewsletterBundle\Exception\TokenCollisionException;
use App\Bundle\NewsletterBundle\Service\EmailValidatorService;
use App\Bundle\NewsletterBundle\Service\SubscriptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\ByteString;

/**
 * Handles operations related to the newsletter subscription,
 * confirmation, unsubscription, and sending functionalities.
 */
class NewsletterController extends AbstractController
{
    /**
     * Subscribes a user to the newsletter.
     *
     * @param Request $request
     * @param SubscriptionService $subscriptionService
     * @param EmailValidatorService $emailValidator
     * @return RedirectResponse
     */
    public function subscribe(
        Request $request,
        SubscriptionService $subscriptionService,
        EmailValidatorService $emailValidator
    ): RedirectResponse
    {
        $email = $request->request->get('semail');
        $referer = $request->headers->get('referer', $this->generateUrl('site_home'));
        
        // Validate the provided email address
        if (!$emailValidator->isValid($email)) {
            $this->addFlash('error', 'Please provide a valid email address.');
            return $this->redirect($referer);
        }

        // Check if the email address is already subscribed
        $existingNewsletter = $subscriptionService->findNewsletterByEmail($email);
        if ($existingNewsletter) {
            // Handle cases where the email is registered but not confirmed
            if (!$existingNewsletter->getIsConfirmed()) {
                $mailerService->sendSubscriptionConfirmation(
                    $this->getParameter('contact_email'),
                    $email,
                    $existingNewsletter->getToken()
                );

                $this->addFlash('error', 'This email address is already registered!');
            } else {
                $this->addFlash('error', 'This email address is already registered.');
            }

            return $this->redirect($referer);
        }
        
        // Create a new subscription and send confirmation email
        try {
            $token = ByteString::fromRandom(55);
            $subscriptionService->subscribe($email, $token);
        } catch (TokenCollisionException $e) {
            $this->addFlash('error', 'The service is temporarily unavailable. Please try again later.');
            return $this->redirect($referer);
        }
        
        // TODO: Send subscription confirmation email
        // ...

        // Notify the user to check email for confirmation
        $this->addFlash('success', 'Thank you for signing up!');
        return $this->redirect($referer);
    }
}
