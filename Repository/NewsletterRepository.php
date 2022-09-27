<?php

namespace App\Bundle\NewsletterBundle\Repository;

use App\Bundle\NewsletterBundle\Entity\Newsletter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Newsletter>
 *
 * @method Newsletter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Newsletter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Newsletter[]    findAll()
 * @method Newsletter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsletterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Newsletter::class);
    }

    /**
     * Retrieve all subscribers who have confirmed their subscription.
     *
     * @return Newsletter[]
     */
    public function findAllConfirmedSubscribers(): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.is_confirmed = :confirmed')
            ->setParameter('confirmed', true)
            ->getQuery()
            ->getResult();
    }
}
