<?php

namespace App\Bundle\NewsletterBundle\Service;

use App\Bundle\NewsletterBundle\Repository\NewsletterRepository;

/**
 * Service class for managing newsletters.
 */
class NewsletterService
{
    /**
     * @var MailerService
     */
    private $mailerService;

    /**
     * @var NewsletterRepository
     */
    private $newsletterRepository;

    /**
     * @param MailerService $mailerService
     * @param NewsletterRepository $newsletterRepository
     */
    public function __construct(
        MailerService $mailerService,
        NewsletterRepository $newsletterRepository
    ) {
        $this->mailerService = $mailerService;
        $this->newsletterRepository = $newsletterRepository;
    }

    /**
     * Send a newsletter to all confirmed subscribers.
     *
     * @param string $contactEmail
     * @param string $subject
     * @param string $title
     * @param string $message
     */
    public function sendToAll(
        string $contactEmail,
        string $subject,
        string $title,
        string $message
    ): void
    {
        $subscribers = $this->newsletterRepository->findAllConfirmedSubscribers();

        foreach ($subscribers as $subscriber) {
            $this->mailerService->sendNewsletter(
                $contactEmail,
                $subscriber->getEmail(),
                $subject,
                $title,
                $message,
                $subscriber->getToken()
            );
        }
    }
}
