<?php

namespace api\WorkLog;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class GetWorkMonthDetailCest
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var User|null
     */
    private $supervisor;

    private function beforeEmployee(\ApiTester $I)
    {
        $this->user = $I->createUser();
        $I->login($this->user);
    }

    private function beforeSupervisor(\ApiTester $I)
    {
        $this->supervisor = $I->createUser();
        $I->login($this->supervisor);

        $this->user = $I->createUser([
            'employeeId' => '123',
            'email' => 'user2@example.com',
            'supervisor' => $this->supervisor,
        ]);
    }

    private function beforeSuperAdmin(\ApiTester $I)
    {
        $this->supervisor = $I->createUser();
        $I->login($this->supervisor);

        $this->user = $I->createUser([
            'employeeId' => '456',
            'email' => 'user3@example.com',
            'roles' => [User::ROLE_SUPER_ADMIN],
        ]);
    }

    private function prepareData(
        \ApiTester $I,
        \DateTimeImmutable $startTime1,
        \DateTimeImmutable $endTime1,
        \DateTimeImmutable $startTime2,
        \DateTimeImmutable $endTime2,
        string $workMonthStatus
    ): array {
        $data['workMonth'] = $I->createWorkMonth([
            'month' => $startTime1->format('m'),
            'status' => $workMonthStatus,
            'user' => $this->user,
            'year' => $I->getSupportedYear($startTime1->format('Y')),
        ]);

        $data['banWorkLog1'] = $I->createBanWorkLog([
            'date' => $startTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['banWorkLog2'] = $I->createBanWorkLog([
            'date' => $endTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['businessTripWorkLog1'] = $I->createBusinessTripWorkLog([
            'date' => $startTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['businessTripWorkLog2'] = $I->createBusinessTripWorkLog([
            'date' => $endTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['homeOfficeWorkLog1'] = $I->createHomeOfficeWorkLog([
            'date' => $startTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['homeOfficeWorkLog2'] = $I->createHomeOfficeWorkLog([
            'date' => $endTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['maternityProtectionWorkLog1'] = $I->createMaternityProtectionWorkLog([
            'date' => $startTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['maternityProtectionWorkLog2'] = $I->createMaternityProtectionWorkLog([
            'date' => $endTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['overtimeWorkLog1'] = $I->createOvertimeWorkLog([
            'date' => $startTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['overtimeWorkLog2'] = $I->createOvertimeWorkLog([
            'date' => $endTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['parentalLeaveWorkLog1'] = $I->createParentalLeaveWorkLog([
            'date' => $startTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['parentalLeaveWorkLog2'] = $I->createParentalLeaveWorkLog([
            'date' => $endTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['sickDayUnpaidWorkLog1'] = $I->createSickDayUnpaidWorkLog([
            'date' => $startTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['sickDayUnpaidWorkLog2'] = $I->createSickDayUnpaidWorkLog([
            'date' => $endTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['sickDayWorkLog1'] = $I->createSickDayWorkLog([
            'date' => $startTime1,
            'variant' => 'WITHOUT_NOTE',
            'workMonth' => $data['workMonth'],
        ]);
        $data['sickDayWorkLog2'] = $I->createSickDayWorkLog([
            'date' => $endTime1,
            'variant' => 'SICK_CHILD',
            'workMonth' => $data['workMonth'],
        ]);
        $data['specialLeaveWorkLog1'] = $I->createSpecialLeaveWorkLog([
            'date' => $startTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['specialLeaveWorkLog2'] = $I->createSpecialLeaveWorkLog([
            'date' => $endTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['timeOffWorkLog1'] = $I->createTimeOffWorkLog([
            'date' => $startTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['timeOffWorkLog2'] = $I->createTimeOffWorkLog([
            'date' => $endTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['trainingWorkLog1'] = $I->createTrainingWorkLog([
            'date' => $startTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['trainingWorkLog2'] = $I->createTrainingWorkLog([
            'date' => $endTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['vacationWorkLog1'] = $I->createVacationWorkLog([
            'date' => $startTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['vacationWorkLog2'] = $I->createVacationWorkLog([
            'date' => $endTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['workLog1'] = $I->createWorkLog([
            'startTime' => $startTime1,
            'endTime' => $endTime1,
            'workMonth' => $data['workMonth'],
        ]);
        $data['workLog2'] = $I->createWorkLog([
            'startTime' => $startTime2,
            'endTime' => $endTime2,
            'workMonth' => $data['workMonth'],
        ]);

        $data['workMonth']->setBanWorkLogs([$data['banWorkLog1'], $data['banWorkLog2']]);
        $data['workMonth']->setBusinessTripWorkLogs([$data['businessTripWorkLog1'], $data['businessTripWorkLog2']]);
        $data['workMonth']->setHomeOfficeWorkLogs([$data['homeOfficeWorkLog1'], $data['homeOfficeWorkLog2']]);
        $data['workMonth']->setMaternityProtectionWorkLogs([$data['maternityProtectionWorkLog1'], $data['maternityProtectionWorkLog2']]);
        $data['workMonth']->setOvertimeWorkLogs([$data['overtimeWorkLog1'], $data['overtimeWorkLog2']]);
        $data['workMonth']->setParentalLeaveWorkLogs([$data['parentalLeaveWorkLog1'], $data['parentalLeaveWorkLog2']]);
        $data['workMonth']->setSickDayUnpaidWorkLogs([$data['sickDayUnpaidWorkLog1'], $data['sickDayUnpaidWorkLog2']]);
        $data['workMonth']->setSickDayWorkLogs([$data['sickDayWorkLog1'], $data['sickDayWorkLog2']]);
        $data['workMonth']->setSpecialLeaveWorkLogs([$data['specialLeaveWorkLog1'], $data['specialLeaveWorkLog2']]);
        $data['workMonth']->setTimeOffWorkLogs([$data['timeOffWorkLog1'], $data['timeOffWorkLog2']]);
        $data['workMonth']->setTrainingWorkLogs([$data['trainingWorkLog1'], $data['trainingWorkLog2']]);
        $data['workMonth']->setVacationWorkLogs([$data['vacationWorkLog1'], $data['vacationWorkLog2']]);
        $data['workMonth']->setWorkLogs([$data['workLog1'], $data['workLog2']]);

        $I->flushToDatabase();

        return $data;
    }

    public function testGetOpenedDetail(\ApiTester $I)
    {
        $this->beforeEmployee($I);

        $startTime1 = new \DateTimeImmutable('2019-06-01T12:00:00');
        $endTime1 = $startTime1->add(new \DateInterval('PT1M'));

        $startTime2 = new \DateTimeImmutable('2019-06-01T12:00:00');
        $endTime2 = $startTime1->add(new \DateInterval('PT1M'));

        $data = $this->prepareData($I, $startTime1, $endTime1, $startTime2, $endTime2, 'OPENED');

        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendGET(sprintf('/work_months/%d/detail', $data['workMonth']->getId()));

        $I->seeHttpHeader('Content-Type', 'application/json');
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseContainsJson([
            'id' => $data['workMonth']->getId(),
            'banWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['banWorkLog1']->getId(),
                    'workTimeLimit' => $data['banWorkLog1']->getWorkTimeLimit(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['banWorkLog2']->getId(),
                    'workTimeLimit' => $data['banWorkLog2']->getWorkTimeLimit(),
                ],
            ],
            'businessTripWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['businessTripWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['businessTripWorkLog2']->getId(),
                ],
            ],
            'homeOfficeWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['homeOfficeWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['homeOfficeWorkLog2']->getId(),
                ],
            ],
            'maternityProtectionWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['maternityProtectionWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['maternityProtectionWorkLog2']->getId(),
                ],
            ],
            'month' => $data['workMonth']->getMonth(),
            'overtimeWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['overtimeWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['overtimeWorkLog2']->getId(),
                ],
            ],
            'parentalLeaveWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['parentalLeaveWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['parentalLeaveWorkLog2']->getId(),
                ],
            ],
            'status' => 'OPENED',
            'sickDayUnpaidWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['sickDayUnpaidWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['sickDayUnpaidWorkLog2']->getId(),
                ],
            ],
            'sickDayWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['sickDayWorkLog1']->getId(),
                    'variant' => $data['sickDayWorkLog1']->getVariant(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['sickDayWorkLog2']->getId(),
                    'variant' => $data['sickDayWorkLog2']->getVariant(),
                ],
            ],
            'specialLeaveWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['specialLeaveWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['specialLeaveWorkLog2']->getId(),
                ],
            ],
            'timeOffWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['timeOffWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['timeOffWorkLog2']->getId(),
                ],
            ],
            'trainingWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['trainingWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['trainingWorkLog2']->getId(),
                ],
            ],
            'user' => ['id' => $this->user->getId()],
            'vacationWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['vacationWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['vacationWorkLog2']->getId(),
                ],
            ],
            'workLogs' => [
                [
                    'startTime' => $startTime1->format(\DateTime::RFC3339),
                    'endTime' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['workLog1']->getId(),
                ],
                [
                    'startTime' => $startTime2->format(\DateTime::RFC3339),
                    'endTime' => $endTime2->format(\DateTime::RFC3339),
                    'id' => $data['workLog2']->getId(),
                ],
            ],
            'year' => [
                'year' => $data['workMonth']->getYear()->getYear(),
            ],
        ]);
    }

    public function testGetWaitingForApprovalDetail(\ApiTester $I)
    {
        $this->beforeEmployee($I);

        $startTime1 = new \DateTimeImmutable('2019-06-01T12:00:00');
        $endTime1 = $startTime1->add(new \DateInterval('PT1M'));

        $startTime2 = new \DateTimeImmutable('2019-06-01T12:00:00');
        $endTime2 = $startTime1->add(new \DateInterval('PT1M'));

        $data = $this->prepareData($I, $startTime1, $endTime1, $startTime2, $endTime2, 'WAITING_FOR_APPROVAL');

        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendGET(sprintf('/work_months/%d/detail', $data['workMonth']->getId()));

        $I->seeHttpHeader('Content-Type', 'application/json');
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseContainsJson([
            'id' => $data['workMonth']->getId(),
            'banWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['banWorkLog1']->getId(),
                    'workTimeLimit' => $data['banWorkLog1']->getWorkTimeLimit(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['banWorkLog2']->getId(),
                    'workTimeLimit' => $data['banWorkLog2']->getWorkTimeLimit(),
                ],
            ],
            'businessTripWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['businessTripWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['businessTripWorkLog2']->getId(),
                ],
            ],
            'homeOfficeWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['homeOfficeWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['homeOfficeWorkLog2']->getId(),
                ],
            ],
            'maternityProtectionWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['maternityProtectionWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['maternityProtectionWorkLog2']->getId(),
                ],
            ],
            'month' => $data['workMonth']->getMonth(),
            'overtimeWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['overtimeWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['overtimeWorkLog2']->getId(),
                ],
            ],
            'parentalLeaveWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['parentalLeaveWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['parentalLeaveWorkLog2']->getId(),
                ],
            ],
            'status' => 'WAITING_FOR_APPROVAL',
            'sickDayUnpaidWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['sickDayUnpaidWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['sickDayUnpaidWorkLog2']->getId(),
                ],
            ],
            'sickDayWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['sickDayWorkLog1']->getId(),
                    'variant' => $data['sickDayWorkLog1']->getVariant(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['sickDayWorkLog2']->getId(),
                    'variant' => $data['sickDayWorkLog2']->getVariant(),
                ],
            ],
            'specialLeaveWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['specialLeaveWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['specialLeaveWorkLog2']->getId(),
                ],
            ],
            'timeOffWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['timeOffWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['timeOffWorkLog2']->getId(),
                ],
            ],
            'trainingWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['trainingWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['trainingWorkLog2']->getId(),
                ],
            ],
            'user' => ['id' => $this->user->getId()],
            'vacationWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['vacationWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['vacationWorkLog2']->getId(),
                ],
            ],
            'workLogs' => [
                [
                    'startTime' => $startTime1->format(\DateTime::RFC3339),
                    'endTime' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['workLog1']->getId(),
                ],
                [
                    'startTime' => $startTime2->format(\DateTime::RFC3339),
                    'endTime' => $endTime2->format(\DateTime::RFC3339),
                    'id' => $data['workLog2']->getId(),
                ],
            ],
            'year' => [
                'year' => $data['workMonth']->getYear()->getYear(),
            ],
        ]);
    }

    public function testGetOpenedDetailAsSupervisor(\ApiTester $I)
    {
        $this->beforeSupervisor($I);

        $startTime1 = new \DateTimeImmutable('2019-06-01T12:00:00');
        $endTime1 = $startTime1->add(new \DateInterval('PT1M'));

        $startTime2 = new \DateTimeImmutable('2019-06-01T12:00:00');
        $endTime2 = $startTime1->add(new \DateInterval('PT1M'));

        $data = $this->prepareData($I, $startTime1, $endTime1, $startTime2, $endTime2, 'OPENED');

        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendGET(sprintf('/work_months/%d/detail', $data['workMonth']->getId()));

        $I->seeHttpHeader('Content-Type', 'application/json');
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseContainsJson([
            'id' => $data['workMonth']->getId(),
            'businessTripWorkLogs' => [],
            'homeOfficeWorkLogs' => [],
            'maternityProtectionWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['maternityProtectionWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['maternityProtectionWorkLog2']->getId(),
                ],
            ],
            'month' => $data['workMonth']->getMonth(),
            'overtimeWorkLogs' => [],
            'parentalLeaveWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['parentalLeaveWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['parentalLeaveWorkLog2']->getId(),
                ],
            ],
            'status' => 'OPENED',
            'sickDayUnpaidWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['sickDayUnpaidWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['sickDayUnpaidWorkLog2']->getId(),
                ],
            ],
            'sickDayWorkLogs' => [],
            'specialLeaveWorkLogs' => [],
            'timeOffWorkLogs' => [],
            'trainingWorkLogs' => [],

            'user' => ['id' => $this->user->getId()],
            'vacationWorkLogs' => [],
            'workLogs' => [],
            'year' => [
                'year' => $data['workMonth']->getYear()->getYear(),
            ],
        ]);
    }

    public function testGetWaitingForApprovalDetailAsSupervisor(\ApiTester $I)
    {
        $this->beforeSupervisor($I);

        $startTime1 = new \DateTimeImmutable('2019-06-01T12:00:00');
        $endTime1 = $startTime1->add(new \DateInterval('PT1M'));

        $startTime2 = new \DateTimeImmutable('2019-06-01T12:00:00');
        $endTime2 = $startTime1->add(new \DateInterval('PT1M'));

        $data = $this->prepareData($I, $startTime1, $endTime1, $startTime2, $endTime2, 'WAITING_FOR_APPROVAL');

        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendGET(sprintf('/work_months/%d/detail', $data['workMonth']->getId()));

        $I->seeHttpHeader('Content-Type', 'application/json');
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseContainsJson([
            'id' => $data['workMonth']->getId(),
            'banWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['banWorkLog1']->getId(),
                    'workTimeLimit' => $data['banWorkLog1']->getWorkTimeLimit(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['banWorkLog2']->getId(),
                    'workTimeLimit' => $data['banWorkLog2']->getWorkTimeLimit(),
                ],
            ],
            'businessTripWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['businessTripWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['businessTripWorkLog2']->getId(),
                ],
            ],
            'homeOfficeWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['homeOfficeWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['homeOfficeWorkLog2']->getId(),
                ],
            ],
            'maternityProtectionWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['maternityProtectionWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['maternityProtectionWorkLog2']->getId(),
                ],
            ],
            'month' => $data['workMonth']->getMonth(),
            'overtimeWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['overtimeWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['overtimeWorkLog2']->getId(),
                ],
            ],
            'parentalLeaveWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['parentalLeaveWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['parentalLeaveWorkLog2']->getId(),
                ],
            ],
            'status' => 'WAITING_FOR_APPROVAL',
            'sickDayUnpaidWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['sickDayUnpaidWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['sickDayUnpaidWorkLog2']->getId(),
                ],
            ],
            'sickDayWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['sickDayWorkLog1']->getId(),
                    'variant' => $data['sickDayWorkLog1']->getVariant(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['sickDayWorkLog2']->getId(),
                    'variant' => $data['sickDayWorkLog2']->getVariant(),
                ],
            ],
            'specialLeaveWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['specialLeaveWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['specialLeaveWorkLog2']->getId(),
                ],
            ],
            'timeOffWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['timeOffWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['timeOffWorkLog2']->getId(),
                ],
            ],
            'trainingWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['trainingWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['trainingWorkLog2']->getId(),
                ],
            ],
            'user' => ['id' => $this->user->getId()],
            'vacationWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['vacationWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['vacationWorkLog2']->getId(),
                ],
            ],
            'workLogs' => [
                [
                    'startTime' => $startTime1->format(\DateTime::RFC3339),
                    'endTime' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['workLog1']->getId(),
                ],
                [
                    'startTime' => $startTime2->format(\DateTime::RFC3339),
                    'endTime' => $endTime2->format(\DateTime::RFC3339),
                    'id' => $data['workLog2']->getId(),
                ],
            ],
            'year' => [
                'year' => $data['workMonth']->getYear()->getYear(),
            ],
        ]);
    }

    public function testGetOpenedDetailAsSuperAdmin(\ApiTester $I)
    {
        $this->beforeSuperAdmin($I);

        $startTime1 = new \DateTimeImmutable('2019-06-01T12:00:00');
        $endTime1 = $startTime1->add(new \DateInterval('PT1M'));

        $startTime2 = new \DateTimeImmutable('2019-06-01T12:00:00');
        $endTime2 = $startTime1->add(new \DateInterval('PT1M'));

        $data = $this->prepareData($I, $startTime1, $endTime1, $startTime2, $endTime2, 'OPENED');

        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendGET(sprintf('/work_months/%d/detail', $data['workMonth']->getId()));

        $I->seeHttpHeader('Content-Type', 'application/json');
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseContainsJson([
            'id' => $data['workMonth']->getId(),
            'banWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['banWorkLog1']->getId(),
                    'workTimeLimit' => $data['banWorkLog1']->getWorkTimeLimit(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['banWorkLog2']->getId(),
                    'workTimeLimit' => $data['banWorkLog2']->getWorkTimeLimit(),
                ],
            ],
            'businessTripWorkLogs' => [],
            'homeOfficeWorkLogs' => [],
            'maternityProtectionWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['maternityProtectionWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['maternityProtectionWorkLog2']->getId(),
                ],
            ],
            'month' => $data['workMonth']->getMonth(),
            'overtimeWorkLogs' => [],
            'parentalLeaveWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['parentalLeaveWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['parentalLeaveWorkLog2']->getId(),
                ],
            ],
            'status' => 'OPENED',
            'sickDayUnpaidWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['sickDayUnpaidWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['sickDayUnpaidWorkLog2']->getId(),
                ],
            ],
            'sickDayWorkLogs' => [],
            'specialLeaveWorkLogs' => [],
            'timeOffWorkLogs' => [],
            'trainingWorkLogs' => [],
            'user' => ['id' => $this->user->getId()],
            'vacationWorkLogs' => [],
            'workLogs' => [],
            'year' => [
                'year' => $data['workMonth']->getYear()->getYear(),
            ],
        ]);
    }

    public function testGetWaitingForApprovalDetailAsSuperAdmin(\ApiTester $I)
    {
        $this->beforeSuperAdmin($I);

        $startTime1 = new \DateTimeImmutable('2019-06-01T12:00:00');
        $endTime1 = $startTime1->add(new \DateInterval('PT1M'));

        $startTime2 = new \DateTimeImmutable('2019-06-01T12:00:00');
        $endTime2 = $startTime1->add(new \DateInterval('PT1M'));

        $data = $this->prepareData($I, $startTime1, $endTime1, $startTime2, $endTime2, 'WAITING_FOR_APPROVAL');

        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendGET(sprintf('/work_months/%d/detail', $data['workMonth']->getId()));

        $I->seeHttpHeader('Content-Type', 'application/json');
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseContainsJson([
            'id' => $data['workMonth']->getId(),
            'banWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['banWorkLog1']->getId(),
                    'workTimeLimit' => $data['banWorkLog1']->getWorkTimeLimit(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['banWorkLog2']->getId(),
                    'workTimeLimit' => $data['banWorkLog2']->getWorkTimeLimit(),
                ],
            ],
            'businessTripWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['businessTripWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['businessTripWorkLog2']->getId(),
                ],
            ],
            'homeOfficeWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['homeOfficeWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['homeOfficeWorkLog2']->getId(),
                ],
            ],
            'maternityProtectionWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['maternityProtectionWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['maternityProtectionWorkLog2']->getId(),
                ],
            ],
            'month' => $data['workMonth']->getMonth(),
            'overtimeWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['overtimeWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['overtimeWorkLog2']->getId(),
                ],
            ],
            'parentalLeaveWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['parentalLeaveWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['parentalLeaveWorkLog2']->getId(),
                ],
            ],
            'status' => 'WAITING_FOR_APPROVAL',
            'sickDayUnpaidWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['sickDayUnpaidWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['sickDayUnpaidWorkLog2']->getId(),
                ],
            ],
            'sickDayWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['sickDayWorkLog1']->getId(),
                    'variant' => $data['sickDayWorkLog1']->getVariant(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['sickDayWorkLog2']->getId(),
                    'variant' => $data['sickDayWorkLog2']->getVariant(),
                ],
            ],
            'specialLeaveWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['specialLeaveWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['specialLeaveWorkLog2']->getId(),
                ],
            ],
            'timeOffWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['timeOffWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['timeOffWorkLog2']->getId(),
                ],
            ],
            'trainingWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['trainingWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['trainingWorkLog2']->getId(),
                ],
            ],
            'user' => ['id' => $this->user->getId()],
            'vacationWorkLogs' => [
                [
                    'date' => $startTime1->format(\DateTime::RFC3339),
                    'id' => $data['vacationWorkLog1']->getId(),
                ],
                [
                    'date' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['vacationWorkLog2']->getId(),
                ],
            ],
            'workLogs' => [
                [
                    'startTime' => $startTime1->format(\DateTime::RFC3339),
                    'endTime' => $endTime1->format(\DateTime::RFC3339),
                    'id' => $data['workLog1']->getId(),
                ],
                [
                    'startTime' => $startTime2->format(\DateTime::RFC3339),
                    'endTime' => $endTime2->format(\DateTime::RFC3339),
                    'id' => $data['workLog2']->getId(),
                ],
            ],
            'year' => [
                'year' => $data['workMonth']->getYear()->getYear(),
            ],
        ]);
    }
}
