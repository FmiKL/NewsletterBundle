<?php

namespace App\Bundle\NewsletterBundle\Controller;

use App\Bundle\NewsletterBundle\Exception\TokenCollisionException;
use App\Bundle\NewsletterBundle\Service\ConfirmationService;
use App\Bundle\NewsletterBundle\Service\EmailValidatorService;
use App\Bundle\NewsletterBundle\Service\MailerService;
use App\Bundle\NewsletterBundle\Service\NewsletterService;
use App\Bundle\NewsletterBundle\Service\SubscriptionService;
use App\Bundle\NewsletterBundle\Service\UnsubscribeService;
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
     * @param MailerService $mailerService
     * @return RedirectResponse
     */
    public function subscribe(
        Request $request,
        SubscriptionService $subscriptionService,
        EmailValidatorService $emailValidator,
        MailerService $mailerService
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

                $this->addFlash('error', 'This email address is already registered! Check your email for confirmation.');
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
        
        // Send subscription confirmation email
        $mailerService->sendSubscriptionConfirmation(
            $this->getParameter('contact_email'),
            $email,
            $token
        );

        // Notify the user to check email for confirmation
        $this->addFlash('success', 'Thank you for signing up! Check your email for confirmation.');
        return $this->redirect($referer);
    }

    /**
     * Confirms the user's subscription using a token.
     *
     * @param Request $request
     * @param ConfirmationService $confirmationService
     * @return RedirectResponse
     */
    public function confirm(
        Request $request,
        ConfirmationService $confirmationService
    ): RedirectResponse
    {
        $token = $request->query->get('token');
        $isConfirmed = $confirmationService->confirm($token);
    
        // Handle subscription confirmation result
        if ($isConfirmed) {
            $this->addFlash('success', 'Your email address has been successfully confirmed.');
        } else {
            $this->addFlash('error', 'This token is no longer valid.');
        }
    
        return $this->redirectToRoute('site_home');
    }

    /**
     * Sends a newsletter to all subscribers.
     *
     * @param Request $request
     * @param NewsletterService $newsletterService
     * @return RedirectResponse
     */
    public function send(
        Request $request,
        NewsletterService $newsletterService
    ): RedirectResponse
    {
        try {
            // Send newsletter to all subscribers
            $newsletterService->sendToAll(
                $this->getParameter('contact_email'),
                $request->get('subject'),
                $request->get('title'),
                $request->get('message')
            );
            
            // Notify admin that newsletter has been sent
            $this->addFlash('success', 'Newsletter sent to all subscribers!');
        } catch (\Exception $e) {
            // Handle errors during sending process
            $this->addFlash('error', 'An error occurred while sending the newsletter: ' . $e->getMessage());
        }

        return $this->redirectToRoute('admin_dashboard');
    }

    /**
     * Unsubscribes the user using a token.
     *
     * @param Request $request
     * @param UnsubscribeService $unsubscribeService
     * @return RedirectResponse
     */
    public function unsubscribe(
        Request $request,
        UnsubscribeService $unsubscribeService
    ): RedirectResponse
    {
        $token = $request->query->get('token');
        $isUnsubscribed = $unsubscribeService->remove($token);
    
        // Handle unsubscription result
        if ($isUnsubscribed) {
            $this->addFlash('success', 'Your email address has been successfully removed.');
        } else {
            $this->addFlash('error', 'This token is no longer valid.');
        }
    
        return $this->redirectToRoute('site_home');
    }
}
