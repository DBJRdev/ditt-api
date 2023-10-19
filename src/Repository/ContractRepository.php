<?php

namespace App\Repository;

use App\Entity\Contract;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\UnexpectedResultException;

class ContractRepository
{
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        /** @var EntityRepository $repository */
        $repository = $entityManager->getRepository(Contract::class);
        $this->repository = $repository;
    }

    public function getRepository(): EntityRepository
    {
        return $this->repository;
    }

    public function findActiveContract(User $user, \DateTimeImmutable $currentDate): ?Contract
    {
        try {
            return $this->repository->createQueryBuilder('c')
                ->where('c.user = :user')
                ->andWhere('c.startDateTime <= :currentDate')
                ->andWhere('c.endDateTime >= :currentDate OR c.endDateTime IS NULL')
                ->setParameter('user', $user->getId())
                ->setParameter('currentDate', $currentDate)
                ->getQuery()
                ->getSingleResult();
        } catch (UnexpectedResultException $e) {
            return null;
        }
    }

    public function findContractsBetweenDates(User $user, \DateTimeImmutable $dateFrom, \DateTimeImmutable $dateTo): array
    {
        return $this->repository->createQueryBuilder('c')
            ->where('c.user = :user')
            ->andWhere('c.startDateTime <= :dateTo')
            ->andWhere('c.endDateTime >= :dateFrom OR c.endDateTime IS NULL')
            ->setParameter('user', $user->getId())
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
            ->getQuery()
            ->getResult();
    }

    public function findContractsAfterDate(User $user, \DateTimeImmutable $date): array
    {
        return $this->repository->createQueryBuilder('c')
            ->where('c.user = :user')
            ->andWhere('c.startDateTime >= :date')
            ->setParameter('user', $user->getId())
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }

    public function findContractsAfter(Contract $contract): array
    {
        return $this->repository->createQueryBuilder('c')
            ->where('c.id != :contractId')
            ->andWhere('c.startDateTime >= :contractEndDateTime')
            ->setParameter('contractId', $contract->getId())
            ->setParameter('contractEndDateTime', $contract->getEndDateTime())
            ->getQuery()
            ->getResult();
    }
}
