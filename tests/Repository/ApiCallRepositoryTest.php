<?php

namespace App\Tests\Repository;

use App\Entity\ApiCall;
use App\Repository\ApiCallRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr;
use PHPUnit\Framework\TestCase;

class ApiCallRepositoryTest extends TestCase
{
    private ApiCallRepository $repository;
    private EntityManagerInterface $entityManager;
    private QueryBuilder $queryBuilder;
    private AbstractQuery $query;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->query = $this->createMock(AbstractQuery::class);

        $this->repository = new ApiCallRepository($this->entityManager);
    }

    public function testGetCallsThisMonth(): void
    {
        $expectedCount = 150;

        $this->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('select')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('from')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('where')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('setParameter')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->method('getSingleScalarResult')
            ->willReturn($expectedCount);

        $result = $this->repository->getCallsThisMonth();

        $this->assertEquals($expectedCount, $result);
    }

    public function testGetCallsToday(): void
    {
        $expectedCount = 25;

        $this->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('select')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('from')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('where')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('setParameter')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->method('getSingleScalarResult')
            ->willReturn($expectedCount);

        $result = $this->repository->getCallsToday();

        $this->assertEquals($expectedCount, $result);
    }

    public function testGetTotalCalls(): void
    {
        $expectedCount = 1250;

        $this->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('select')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('from')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->method('getSingleScalarResult')
            ->willReturn($expectedCount);

        $result = $this->repository->getTotalCalls();

        $this->assertEquals($expectedCount, $result);
    }

    public function testGetSuccessfulCallsThisMonth(): void
    {
        $expectedCount = 140;

        $this->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('select')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('from')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('where')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('andWhere')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('setParameter')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->method('getSingleScalarResult')
            ->willReturn($expectedCount);

        $result = $this->repository->getSuccessfulCallsThisMonth();

        $this->assertEquals($expectedCount, $result);
    }

    public function testGetAverageResponseTimeThisMonth(): void
    {
        $expectedAverage = 125.5;

        $this->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('select')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('from')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('where')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('andWhere')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('setParameter')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->method('getSingleScalarResult')
            ->willReturn($expectedAverage);

        $result = $this->repository->getAverageResponseTimeThisMonth();

        $this->assertEquals($expectedAverage, $result);
    }

    public function testGetTopEndpointsThisMonth(): void
    {
        $expectedEndpoints = [
            ['endpoint' => '/api/contacts', 'count' => 45],
            ['endpoint' => '/api/users', 'count' => 32],
            ['endpoint' => '/api/login', 'count' => 28],
        ];

        $this->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('select')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('from')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('where')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('groupBy')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('orderBy')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('setParameter')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('setMaxResults')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->method('getResult')
            ->willReturn($expectedEndpoints);

        $result = $this->repository->getTopEndpointsThisMonth(3);

        $this->assertEquals($expectedEndpoints, $result);
    }

    public function testGetRecentCalls(): void
    {
        $expectedCalls = [
            new ApiCall(),
            new ApiCall(),
            new ApiCall(),
        ];

        $this->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('select')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('from')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('orderBy')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('setMaxResults')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->method('getResult')
            ->willReturn($expectedCalls);

        $result = $this->repository->getRecentCalls(3);

        $this->assertEquals($expectedCalls, $result);
    }

    public function testGetCallsByUserThisMonth(): void
    {
        $userIdentifier = 'user@example.com';
        $expectedCount = 15;

        $this->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('select')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('from')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('where')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('andWhere')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('setParameter')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->method('getSingleScalarResult')
            ->willReturn($expectedCount);

        $result = $this->repository->getCallsByUserThisMonth($userIdentifier);

        $this->assertEquals($expectedCount, $result);
    }

    public function testGetFailedCallsThisMonth(): void
    {
        $expectedCount = 10;

        $this->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('select')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('from')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('where')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('andWhere')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('setParameter')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->method('getSingleScalarResult')
            ->willReturn($expectedCount);

        $result = $this->repository->getFailedCallsThisMonth();

        $this->assertEquals($expectedCount, $result);
    }

    public function testGetCallsByMethodThisMonth(): void
    {
        $method = 'POST';
        $expectedCount = 85;

        $this->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder
            ->method('select')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('from')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('where')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('andWhere')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('setParameter')
            ->willReturnSelf();

        $this->queryBuilder
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query
            ->method('getSingleScalarResult')
            ->willReturn($expectedCount);

        $result = $this->repository->getCallsByMethodThisMonth($method);

        $this->assertEquals($expectedCount, $result);
    }
} 