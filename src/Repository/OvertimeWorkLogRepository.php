<?php

namespace App\Repository;

use App\Entity\OvertimeWorkLog;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class OvertimeWorkLogRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        /** @var EntityRepository $repository */
        $repository = $entityManager->getRepository(OvertimeWorkLog::class);

        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * @return EntityRepository
     */
    public function getRepository(): EntityRepository
    {
        return $this->repository;
    }

    /**
     * @return mixed
     */
    public function findAllWaitingForApproval()
    {
        $qb = $this->repository->createQueryBuilder('owl');

        return $qb
            ->select('owl')
            ->leftJoin('owl.workMonth', 'wm')
            ->leftJoin('wm.user', 'u')
            ->where($qb->expr()->andX(
                $qb->expr()->isNull('owl.timeApproved'),
                $qb->expr()->isNull('owl.timeRejected')
            ))
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User $supervisor
     * @return mixed
     */
    public function findAllWaitingForApprovalBySupervisor(User $supervisor)
    {
        $qb = $this->repository->createQueryBuilder('owl');

        return $qb
            ->select('owl')
            ->leftJoin('owl.workMonth', 'wm')
            ->leftJoin('wm.user', 'u')
            ->where($qb->expr()->andX(
                $qb->expr()->eq('u.supervisor', ':supervisor'),
                $qb->expr()->isNull('owl.timeApproved'),
                $qb->expr()->isNull('owl.timeRejected')
            ))
            ->setParameter('supervisor', $supervisor)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return OvertimeWorkLog[]
     */
    public function findAllRecent(): array
    {
        $date = new \DateTime();
        $currentMonth = $date->format('m');
        $currentYear = $date->format('Y');

        $date->modify('-1 month');
        $previousMonth = $date->format('m');
        $previousYear = $date->format('Y');

        $qb = $this->repository->createQueryBuilder('owl');

        return $qb
            ->select('owl')
            ->leftJoin('owl.workMonth', 'wm')
            ->leftJoin('wm.user', 'u')
            ->where($qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->eq('wm.month', ':currentMonth'),
                    $qb->expr()->eq('wm.year', ':currentYear')
                ),
                $qb->expr()->andX(
                    $qb->expr()->eq('wm.month', ':previousMonth'),
                    $qb->expr()->eq('wm.year', ':previousYear')
                )
            ))
            ->setParameter('currentMonth', $currentMonth)
            ->setParameter('currentYear', $currentYear)
            ->setParameter('previousMonth', $previousMonth)
            ->setParameter('previousYear', $previousYear)
            ->orderBy('owl.date', 'desc')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User $supervisor
     * @return OvertimeWorkLog[]
     */
    public function findAllRecentBySupervisor(User $supervisor): array
    {
        $date = new \DateTime();
        $currentMonth = $date->format('m');
        $currentYear = $date->format('Y');

        $date->modify('-1 month');
        $previousMonth = $date->format('m');
        $previousYear = $date->format('Y');

        $qb = $this->repository->createQueryBuilder('owl');

        return $qb
            ->select('owl')
            ->leftJoin('owl.workMonth', 'wm')
            ->leftJoin('wm.user', 'u')
            ->where($qb->expr()->andX(
                $qb->expr()->eq('u.supervisor', ':supervisor'),
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->eq('wm.month', ':currentMonth'),
                        $qb->expr()->eq('wm.year', ':currentYear')
                    ),
                    $qb->expr()->andX(
                        $qb->expr()->eq('wm.month', ':previousMonth'),
                        $qb->expr()->eq('wm.year', ':previousYear')
                    )
                )
            ))
            ->setParameter('supervisor', $supervisor)
            ->setParameter('currentMonth', $currentMonth)
            ->setParameter('currentYear', $currentYear)
            ->setParameter('previousMonth', $previousMonth)
            ->setParameter('previousYear', $previousYear)
            ->orderBy('owl.date', 'desc')
            ->getQuery()
            ->getResult();
    }
}