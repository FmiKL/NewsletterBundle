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
     * Path to the news email template.
     * 
     * @var string
     */
    private const NEWS_TEMPLATE_PATH = '@NewsletterBundle/emails/news.html.twig';

    /**
     * @param MailerInterface $mailer
     */
    public function __construct(
        private MailerInterface $mailer
    ){}

    /**
     * Send a newsletter email.
     *
     * @param string $contactEmail
     * @param string $userEmail
     * @param string $subject
     * @param string $title
     * @param string $message
     * @param string $token
     */
    public function sendNewsletter(
        string $contactEmail,
        string $userEmail,
        string $subject,
        string $title,
        string $message,
        string $token
    ): void
    {
        $context = compact('title', 'message', 'token');
        $this->sendEmail($contactEmail, $userEmail, $subject, self::NEWS_TEMPLATE_PATH, $context);
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
        $context = ['token' => $token];
        $this->sendEmail($contactEmail, $userEmail, 'Confirmation of your email address', self::SUBSCRIPTION_TEMPLATE_PATH, $context);
    }

    /**
     * General method for sending an email.
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $templatePath
     * @param array $context
     */
    private function sendEmail(
        string $from,
        string $to,
        string $subject,
        string $templatePath,
        array $context
    ): void
    {
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($templatePath)
            ->context($context);

        $this->mailer->send($email);
    }
}
