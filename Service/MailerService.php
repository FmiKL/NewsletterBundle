<?php

namespace App\Bundle\NewsletterBundle\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

/**
 * Service class for sending various types of emails.
 */
class MailerService
{
    /**
     * Path to the subscription confirmation email template.
     * 
     * @var string
     */
    private const SUBSCRIPTION_TEMPLATE_PATH = '@NewsletterBundle/emails/confirm.html.twig';

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send a subscription confirmation email.
     *
     * @param string $contactEmail
     * @param string $userEmail
     * @param string $token
     */
    public function sendSubscriptionConfirmation(
        string $contactEmail,
        string $userEmail,
        string $token
    ): void
    {
        $email = (new TemplatedEmail())
            ->from($contactEmail)
            ->to($userEmail)
            ->subject('Confirmation of your email address')
            ->htmlTemplate(self::SUBSCRIPTION_TEMPLATE_PATH)
            ->context(['token' => $token]);

        $this->mailer->send($email);
    }
}
