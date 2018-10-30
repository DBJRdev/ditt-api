<?php

namespace unit\Service;

use App\Entity\BusinessTripWorkLog;
use App\Entity\SickDayWorkLog;
use App\Entity\User;
use App\Entity\VacationWorkLog;
use App\Entity\WorkHours;
use App\Entity\WorkLog;
use App\Entity\WorkMonth;
use App\Repository\BusinessTripWorkLogRepository;
use App\Repository\SickDayWorkLogRepository;
use App\Repository\UserYearStatsRepository;
use App\Repository\VacationWorkLogRepository;
use App\Repository\WorkHoursRepository;
use App\Repository\WorkLogRepository;
use App\Service\WorkMonthService;
use Doctrine\ORM\EntityManager;
use Prophecy\Argument;
use Prophecy\Prophet;

class WorkMonthServiceCest
{
    /**
     * @param \UnitTester $I
     * @throws \Exception
     */
    public function testCalculateNoWorkLogs(\UnitTester $I): void
    {
        $prophet = new Prophet();
        $workMonth = $this->getWorkMonth($prophet);
        $workHours = $this->getWorkHours($prophet);

        $service = $this->getWorkMonthService($prophet, [], [], [], [], $workHours);

        $I->assertEquals(0, $service->calculateWorkedHours($workMonth));
    }

    /**
     * @param \UnitTester $I
     * @throws \Exception
     */
    public function testCalculateWorkLogs(\UnitTester $I): void
    {
        $prophet = new Prophet();
        $workMonth = $this->getWorkMonth($prophet);
        $workHours = $this->getWorkHours($prophet);

        $workLogs = [
            (new WorkLog())
                ->setWorkMonth($workMonth)
                ->setStartTime(new \DateTimeImmutable('2018-01-01 10:00:00'))
                ->setEndTime(new \DateTimeImmutable('2018-01-01 12:00:00')),
            (new WorkLog())
                ->setWorkMonth($workMonth)
                ->setStartTime(new \DateTimeImmutable('2018-01-01 14:00:00'))
                ->setEndTime(new \DateTimeImmutable('2018-01-01 16:00:00')),
        ];

        $service = $this->getWorkMonthService($prophet, [], [], [], $workLogs, $workHours);

        $I->assertEquals(4, $service->calculateWorkedHours($workMonth));
    }

    /**
     * @param \UnitTester $I
     * @throws \Exception
     */
    public function testCalculateWorkLogsAboveLowerLimit(\UnitTester $I): void
    {
        $prophet = new Prophet();
        $workMonth = $this->getWorkMonth($prophet);
        $workHours = $this->getWorkHours($prophet);

        $workLogs = [
            (new WorkLog())
                ->setWorkMonth($workMonth)
                ->setStartTime(new \DateTimeImmutable('2018-01-01 08:00:00'))
                ->setEndTime(new \DateTimeImmutable('2018-01-01 12:00:00')),
            (new WorkLog())
                ->setWorkMonth($workMonth)
                ->setStartTime(new \DateTimeImmutable('2018-01-01 14:00:00'))
                ->setEndTime(new \DateTimeImmutable('2018-01-01 17:00:00')),
        ];

        $service = $this->getWorkMonthService($prophet, [], [], [], $workLogs, $workHours);

        $I->assertEquals(6.5, $service->calculateWorkedHours($workMonth));
    }

    /**
     * @param \UnitTester $I
     * @throws \Exception
     */
    public function testCalculateWorkLogsAboveUpperLimit(\UnitTester $I): void
    {
        $prophet = new Prophet();
        $workMonth = $this->getWorkMonth($prophet);
        $workHours = $this->getWorkHours($prophet);

        $workLogs = [
            (new WorkLog())
                ->setWorkMonth($workMonth)
                ->setStartTime(new \DateTimeImmutable('2018-01-01 08:00:00'))
                ->setEndTime(new \DateTimeImmutable('2018-01-01 12:00:00')),
            (new WorkLog())
                ->setWorkMonth($workMonth)
                ->setStartTime(new \DateTimeImmutable('2018-01-01 14:00:00'))
                ->setEndTime(new \DateTimeImmutable('2018-01-01 20:00:00')),
        ];

        $service = $this->getWorkMonthService($prophet, [], [], [], $workLogs, $workHours);

        $I->assertEquals(9.25, $service->calculateWorkedHours($workMonth));
    }

    /**
     * @param \UnitTester $I
     * @throws \Exception
     */
    public function testCalculateApprovedBusinessTripWorkLogsWithoutWorkLogs(\UnitTester $I): void
    {
        $prophet = new Prophet();
        $workMonth = $this->getWorkMonth($prophet);
        $workHours = $this->getWorkHours($prophet);

        $businessTripWorkLogs = [
            (new BusinessTripWorkLog())
                ->setDate(new \DateTimeImmutable('2018-01-01'))
                ->setTimeApproved(new \DateTimeImmutable('2018-01-01 23:59:59')),
        ];

        $service = $this->getWorkMonthService($prophet, $businessTripWorkLogs, [], [], [], $workHours);

        $I->assertEquals(6, $service->calculateWorkedHours($workMonth));
    }

    /**
     * @param \UnitTester $I
     * @throws \Exception
     */
    public function testCalculateUnapprovedBusinessTripWorkLogsWithoutWorkLogs(\UnitTester $I): void
    {
        $prophet = new Prophet();
        $workMonth = $this->getWorkMonth($prophet);
        $workHours = $this->getWorkHours($prophet);

        $businessTripWorkLogs = [
            (new BusinessTripWorkLog())
                ->setDate(new \DateTimeImmutable('2018-01-01')),
        ];

        $service = $this->getWorkMonthService($prophet, $businessTripWorkLogs, [], [], [], $workHours);

        $I->assertEquals(0, $service->calculateWorkedHours($workMonth));
    }

    /**
     * @param \UnitTester $I
     * @throws \Exception
     */
    public function testCalculateApprovedBusinessTripWorkLogs(\UnitTester $I): void
    {
        $prophet = new Prophet();
        $workMonth = $this->getWorkMonth($prophet);
        $workHours = $this->getWorkHours($prophet);

        $businessTripWorkLogs = [
            (new BusinessTripWorkLog())
                ->setDate(new \DateTimeImmutable('2018-01-01'))
                ->setTimeApproved(new \DateTimeImmutable('2018-01-01 23:59:59')),
        ];
        $workLogs = [
            (new WorkLog())
                ->setStartTime(new \DateTimeImmutable('2018-01-01 10:00:00'))
                ->setEndTime(new \DateTimeImmutable('2018-01-01 12:00:00')),
            (new WorkLog())
                ->setStartTime(new \DateTimeImmutable('2018-01-01 14:00:00'))
                ->setEndTime(new \DateTimeImmutable('2018-01-01 16:00:00')),
        ];

        $service = $this->getWorkMonthService($prophet, $businessTripWorkLogs, [], [], $workLogs, $workHours);

        $I->assertEquals(6, $service->calculateWorkedHours($workMonth));
    }

    /**
     * @param \UnitTester $I
     * @throws \Exception
     */
    public function testCalculateApprovedBusinessTripWorkLogsAboveLowerLimit(\UnitTester $I): void
    {
        $prophet = new Prophet();
        $workMonth = $this->getWorkMonth($prophet);
        $workHours = $this->getWorkHours($prophet);

        $businessTripWorkLogs = [
            (new BusinessTripWorkLog())
                ->setDate(new \DateTimeImmutable('2018-01-01'))
                ->setTimeApproved(new \DateTimeImmutable('2018-01-01 23:59:59')),
        ];
        $workLogs = [
            (new WorkLog())
                ->setStartTime(new \DateTimeImmutable('2018-01-01 08:00:00'))
                ->setEndTime(new \DateTimeImmutable('2018-01-01 12:00:00')),
            (new WorkLog())
                ->setStartTime(new \DateTimeImmutable('2018-01-01 14:00:00'))
                ->setEndTime(new \DateTimeImmutable('2018-01-01 17:00:00')),
        ];

        $service = $this->getWorkMonthService($prophet, $businessTripWorkLogs, [], [], $workLogs, $workHours);

        $I->assertEquals(6.5, $service->calculateWorkedHours($workMonth));
    }

    /**
     * @param \UnitTester $I
     * @throws \Exception
     */
    public function testCalculateApprovedBusinessTripWorkLogsAboveUpperLimit(\UnitTester $I): void
    {
        $prophet = new Prophet();
        $workMonth = $this->getWorkMonth($prophet);
        $workHours = $this->getWorkHours($prophet);

        $businessTripWorkLogs = [
            (new BusinessTripWorkLog())
                ->setDate(new \DateTimeImmutable('2018-01-01'))
                ->setTimeApproved(new \DateTimeImmutable('2018-01-01 23:59:59')),
        ];
        $workLogs = [
            (new WorkLog())
                ->setStartTime(new \DateTimeImmutable('2018-01-01 08:00:00'))
                ->setEndTime(new \DateTimeImmutable('2018-01-01 12:00:00')),
            (new WorkLog())
                ->setStartTime(new \DateTimeImmutable('2018-01-01 14:00:00'))
                ->setEndTime(new \DateTimeImmutable('2018-01-01 20:00:00')),
        ];

        $service = $this->getWorkMonthService($prophet, $businessTripWorkLogs, [], [], $workLogs, $workHours);

        $I->assertEquals(9.25, $service->calculateWorkedHours($workMonth));
    }

    /**
     * @param \UnitTester $I
     * @throws \Exception
     */
    public function testCalculateSickDayWorkLogs(\UnitTester $I): void
    {
        $prophet = new Prophet();
        $workMonth = $this->getWorkMonth($prophet);
        $workHours = $this->getWorkHours($prophet);

        $sickDayWorkLogs = [
            (new SickDayWorkLog())
                ->setDate(new \DateTimeImmutable('2018-01-01')),
        ];

        $service = $this->getWorkMonthService($prophet, [], $sickDayWorkLogs, [], [], $workHours);

        $I->assertEquals(6, $service->calculateWorkedHours($workMonth));
    }

    /**
     * @param \UnitTester $I
     * @throws \Exception
     */
    public function testCalculateApprovedVacationWorkLogs(\UnitTester $I): void
    {
        $prophet = new Prophet();
        $workMonth = $this->getWorkMonth($prophet);
        $workHours = $this->getWorkHours($prophet);

        $vacationWorkLogs = [
            (new VacationWorkLog())
                ->setTimeApproved(new \DateTimeImmutable('2018-01-01 23:59:59'))
                ->setDate(new \DateTimeImmutable('2018-01-01')),
        ];

        $service = $this->getWorkMonthService($prophet, [], [], $vacationWorkLogs, [], $workHours);

        $I->assertEquals(6, $service->calculateWorkedHours($workMonth));
    }

    /**
     * @param \UnitTester $I
     * @throws \Exception
     */
    public function testCalculateUnapprovedVacationWorkLogs(\UnitTester $I): void
    {
        $prophet = new Prophet();
        $workMonth = $this->getWorkMonth($prophet);
        $workHours = $this->getWorkHours($prophet);

        $vacationWorkLogs = [
            (new VacationWorkLog())
                ->setDate(new \DateTimeImmutable('2018-01-01')),
        ];

        $service = $this->getWorkMonthService($prophet, [], [], $vacationWorkLogs, [], $workHours);

        $I->assertEquals(0, $service->calculateWorkedHours($workMonth));
    }

    /**
     * @param \UnitTester $I
     * @throws \Exception
     */
    public function testAll(\UnitTester $I): void
    {
        $prophet = new Prophet();
        $workMonth = $this->getWorkMonth($prophet);
        $workHours = $this->getWorkHours($prophet);

        $businessTripWorkLogs = [
            (new BusinessTripWorkLog())
                ->setDate(new \DateTimeImmutable('2018-01-01'))
                ->setTimeApproved(new \DateTimeImmutable('2018-01-01 23:59:59')),
        ];
        $workLogs = [
            (new WorkLog())
                ->setStartTime(new \DateTimeImmutable('2018-01-01 10:00:00'))
                ->setEndTime(new \DateTimeImmutable('2018-01-01 12:00:00')),
            (new WorkLog())
                ->setStartTime(new \DateTimeImmutable('2018-01-01 14:00:00'))
                ->setEndTime(new \DateTimeImmutable('2018-01-01 18:15:00')),
        ];
        $sickDayWorkLogs = [
            (new SickDayWorkLog())
                ->setDate(new \DateTimeImmutable('2018-01-01')),
        ];
        $vacationWorkLogs = [
            (new VacationWorkLog())
                ->setDate(new \DateTimeImmutable('2018-01-01'))
                ->setTimeApproved(new \DateTimeImmutable('2018-01-01 23:59:59')),
        ];

        $service = $this->getWorkMonthService(
            $prophet,
            $businessTripWorkLogs,
            $sickDayWorkLogs,
            $vacationWorkLogs,
            $workLogs,
            $workHours
        );

        $I->assertEquals(18, $service->calculateWorkedHours($workMonth));
    }

    /**
     * @param Prophet $prophet
     * @return WorkMonth
     */
    private function getWorkMonth(Prophet $prophet): WorkMonth
    {
        $workMonth = $prophet->prophesize(WorkMonth::class);
        $workMonth->getYear()->willReturn(2018);
        $workMonth->getMonth()->willReturn(1);
        $workMonth->getUser()->willReturn(new User());

        return $workMonth->reveal();
    }

    /**
     * @param Prophet $prophet
     * @return WorkHours
     */
    private function getWorkHours(Prophet $prophet): WorkHours
    {
        $workMonth = $prophet->prophesize(WorkHours::class);
        $workMonth->getYear()->willReturn(2018);
        $workMonth->getMonth()->willReturn(1);
        $workMonth->getRequiredHours()->willReturn(6);
        $workMonth->getUser()->willReturn(new User());

        return $workMonth->reveal();
    }

    /**
     * @param Prophet $prophet
     * @return EntityManager
     */
    private function getEntityManager(Prophet $prophet): EntityManager
    {
        $entityManager = $prophet->prophesize(EntityManager::class);

        return $entityManager->reveal();
    }

    /**
     * @param Prophet $prophet
     * @param array $workLogs
     * @return BusinessTripWorkLogRepository
     */
    private function getBusinessTripWorkLogRepository(Prophet $prophet, array $workLogs): BusinessTripWorkLogRepository
    {
        $repository = $prophet->prophesize(BusinessTripWorkLogRepository::class);
        $repository->findAllApprovedByWorkMonth(Argument::type(WorkMonth::class))->willReturn($workLogs);

        return $repository->reveal();
    }

    /**
     * @param Prophet $prophet
     * @param array $workLogs
     * @return SickDayWorkLogRepository
     */
    private function getSickDayWorkLogRepository(Prophet $prophet, array $workLogs): SickDayWorkLogRepository
    {
        $repository = $prophet->prophesize(SickDayWorkLogRepository::class);
        $repository->findAllByWorkMonth(Argument::type(WorkMonth::class))->willReturn($workLogs);

        return $repository->reveal();
    }

    /**
     * @param Prophet $prophet
     * @return UserYearStatsRepository
     */
    private function getUserYearStatsRepository(Prophet $prophet): UserYearStatsRepository
    {
        $repository = $prophet->prophesize(UserYearStatsRepository::class);

        return $repository->reveal();
    }

    /**
     * @param Prophet $prophet
     * @param array $workLogs
     * @return VacationWorkLogRepository
     */
    private function getVacationWorkLogRepository(Prophet $prophet, array $workLogs): VacationWorkLogRepository
    {
        $repository = $prophet->prophesize(VacationWorkLogRepository::class);
        $repository->findAllApprovedByWorkMonth(Argument::type(WorkMonth::class))->willReturn($workLogs);

        return $repository->reveal();
    }

    /**
     * @param Prophet $prophet
     * @param array $workLogs
     * @return WorkLogRepository
     */
    private function getWorkLogRepository(Prophet $prophet, array $workLogs): WorkLogRepository
    {
        $repository = $prophet->prophesize(WorkLogRepository::class);
        $repository->findAllByWorkMonth(Argument::type(WorkMonth::class))->willReturn($workLogs);

        return $repository->reveal();
    }

    /**
     * @param Prophet $prophet
     * @param WorkHours $workHours
     * @return WorkHoursRepository
     */
    private function getWorkHoursRepository(Prophet $prophet, WorkHours $workHours): WorkHoursRepository
    {
        $repository = $prophet->prophesize(WorkHoursRepository::class);
        $repository->findOne(
            Argument::type('int'),
            Argument::type('int'),
            Argument::type(User::class)
        )->willReturn($workHours);

        return $repository->reveal();
    }

    /**
     * @param Prophet $prophet
     * @param BusinessTripWorkLog[] $businessTripWorkLogs
     * @param SickDayWorkLog[] $sickDayWorkLogs
     * @param VacationWorkLog[] $vacationWorkLogs
     * @param WorkLog[] $workLogs
     * @param WorkHours $workHours
     * @return WorkMonthService
     */
    private function getWorkMonthService(
        Prophet $prophet,
        array $businessTripWorkLogs,
        array $sickDayWorkLogs,
        array $vacationWorkLogs,
        array $workLogs,
        WorkHours $workHours
    ): WorkMonthService {
        return new WorkMonthService(
            $this->getEntityManager($prophet),
            $this->getBusinessTripWorkLogRepository($prophet, $businessTripWorkLogs),
            $this->getSickDayWorkLogRepository($prophet, $sickDayWorkLogs),
            $this->getUserYearStatsRepository($prophet),
            $this->getVacationWorkLogRepository($prophet, $vacationWorkLogs),
            $this->getWorkLogRepository($prophet, $workLogs),
            $this->getWorkHoursRepository($prophet, $workHours)
        );
    }
}