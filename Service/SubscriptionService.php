<?php

namespace App\Bundle\NewsletterBundle\Service;

use App\Bundle\NewsletterBundle\Entity\Newsletter;
use App\Bundle\NewsletterBundle\Repository\NewsletterRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Service class for managing newsletter subscriptions.
 */
class SubscriptionService
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param NewsletterRepository $newsletterRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private NewsletterRepository $newsletterRepository,
        private LoggerInterface $logger
    ){}

    /**
     * Find and return a newsletter subscription by email address.
     * 
     * @param string $email
     * @return Newsletter|null
     */
    public function findNewsletterByEmail(string $email): ?Newsletter
    {
        return $this->newsletterRepository->findOneByEmail($email);
    }

    /**
     * Create a new subscription for a given email address and token.
     *
     * @param string $email
     * @param string $token
     * @return Newsletter|null
     */
    public function subscribe(string $email, string $token): ?Newsletter
    {
        $newsletter = new Newsletter();
        $newsletter->setEmail($email);
        $newsletter->setToken($token);

        try {
            $this->entityManager->persist($newsletter);
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException $e) {
            $this->logger->error('Unique constraint violation', ['exception' => $e]);
            return null;
        }

        return $newsletter;
    }
}
