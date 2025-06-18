<?php

namespace App\Repository;

use App\Entity\ApiCall;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApiCall>
 *
 * @method ApiCall|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiCall|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiCall[]    findAll()
 * @method ApiCall[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiCallRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiCall::class);
    }

    public function getTotalCalls(): int
    {
        return $this->count([]);
    }

    public function getCallsThisMonth(): int
    {
        $startOfMonth = new \DateTime('first day of this month');
        $endOfMonth = new \DateTime('last day of this month 23:59:59');

        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.createdAt >= :start')
            ->andWhere('a.createdAt <= :end')
            ->setParameter('start', $startOfMonth)
            ->setParameter('end', $endOfMonth)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getCallsToday(): int
    {
        $startOfDay = new \DateTime('today');
        $endOfDay = new \DateTime('tomorrow -1 second');

        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.createdAt >= :start')
            ->andWhere('a.createdAt <= :end')
            ->setParameter('start', $startOfDay)
            ->setParameter('end', $endOfDay)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getSuccessfulCallsThisMonth(): int
    {
        $startOfMonth = new \DateTime('first day of this month');
        $endOfMonth = new \DateTime('last day of this month 23:59:59');

        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.createdAt >= :start')
            ->andWhere('a.createdAt <= :end')
            ->andWhere('a.statusCode >= 200')
            ->andWhere('a.statusCode < 300')
            ->setParameter('start', $startOfMonth)
            ->setParameter('end', $endOfMonth)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getAverageResponseTimeThisMonth(): float
    {
        $startOfMonth = new \DateTime('first day of this month');
        $endOfMonth = new \DateTime('last day of this month 23:59:59');

        $result = $this->createQueryBuilder('a')
            ->select('AVG(a.responseTime)')
            ->where('a.createdAt >= :start')
            ->andWhere('a.createdAt <= :end')
            ->andWhere('a.responseTime IS NOT NULL')
            ->setParameter('start', $startOfMonth)
            ->setParameter('end', $endOfMonth)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? round($result, 2) : 0;
    }

    public function getTopEndpointsThisMonth(int $limit = 5): array
    {
        $startOfMonth = new \DateTime('first day of this month');
        $endOfMonth = new \DateTime('last day of this month 23:59:59');

        return $this->createQueryBuilder('a')
            ->select('a.endpoint, COUNT(a.id) as callCount')
            ->where('a.createdAt >= :start')
            ->andWhere('a.createdAt <= :end')
            ->setParameter('start', $startOfMonth)
            ->setParameter('end', $endOfMonth)
            ->groupBy('a.endpoint')
            ->orderBy('callCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getRecentCalls(int $limit = 10): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
} 