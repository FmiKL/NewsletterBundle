<?php

namespace App\Bundle\NewsletterBundle\Service;

use App\Bundle\NewsletterBundle\Repository\NewsletterRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service class for managing newsletter unsubscriptions.
 */
class UnsubscribeService
{
    /**
     * @param NewsletterRepository $newsletterRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private NewsletterRepository $newsletterRepository,
        private EntityManagerInterface $entityManager
    ){}

    /**
     * Remove a newsletter subscription by a given token.
     *
     * @param string $token
     * @return bool
     */
    public function remove(string $token): bool
    {
        $newsletter = $this->newsletterRepository->findOneByToken($token);

        if (!$newsletter) {
            return false;
        }

        $this->entityManager->remove($newsletter);
        $this->entityManager->flush();

        return true;
    }
}
