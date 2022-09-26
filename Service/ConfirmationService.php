<?php

namespace App\Bundle\NewsletterBundle\Service;

use App\Bundle\NewsletterBundle\Repository\NewsletterRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service class to handle the confirmation
 * of newsletter subscriptions.
 */
class ConfirmationService
{
    /**
     * @var NewsletterRepository
     */
    private $newsletterRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param NewsletterRepository $newsletterRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        NewsletterRepository $newsletterRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->newsletterRepository = $newsletterRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Confirm a newsletter subscription using a token.
     *
     * @param string $token
     * @return bool
     */
    public function confirm(string $token): bool
    {
        $newsletter = $this->newsletterRepository->findOneByToken($token);

        if (!$newsletter) {
            return false;
        }

        $newsletter->setIsConfirmed(true);

        $this->entityManager->persist($newsletter);
        $this->entityManager->flush();

        return true;
    }
}
