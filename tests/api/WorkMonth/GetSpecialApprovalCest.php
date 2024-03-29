<?php

namespace api\WorkMonth;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class GetSpecialApprovalCest
{
    private function beforeEmployee(\ApiTester $I)
    {
        $user = $I->createUser();
        $I->login($user);
    }

    private function beforeSuperAdmin(\ApiTester $I)
    {
        $user = $I->createUser(['roles' => [User::ROLE_SUPER_ADMIN]]);
        $I->login($user);
    }

    public function testGetEmpty(\ApiTester $I)
    {
        $this->beforeEmployee($I);

        $user1 = $I->createUser([
            'email' => 'user1@example.com',
            'employeeId' => 'id123',
        ]);
        $user2 = $I->createUser([
            'email' => 'user2@example.com',
            'employeeId' => 'id456',
            'supervisor' => $user1,
        ]);

        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendGET(sprintf('/special_approvals/%d', $user2->getId()));

        $I->seeHttpHeader('Content-Type', 'application/json');
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseContainsJson([]);
    }

//    TODO: Fix test
//
//    public function testGetAllAsSupervisor(\ApiTester $I)
//    {
//        $this->beforeEmployee($I);
//
//        $user1 = $I->createUser(['email' => 'user2@example.com', 'employeeId' => 'id123']);
//        $user2 = $I->createUser([
//            'email' => 'user1@example.com',
//            'employeeId' => 'id456',
//            'supervisor' => $user1,
//        ]);
//
//        $workMonth1 = $I->createWorkMonth(['user' => $user1]);
//        $workMonth2 = $I->createWorkMonth(['user' => $user2]);
//
//        $businessTripWorkLog1 = $I->createBusinessTripWorkLog([
//            'date' => new \DateTimeImmutable(),
//            'workMonth' => $workMonth2,
//        ]);
//        $businessTripWorkLog2 = $I->createBusinessTripWorkLog([
//            'date' => new \DateTimeImmutable(),
//            'timeApproved' => new \DateTimeImmutable(),
//            'workMonth' => $workMonth2,
//        ]);
//        $businessTripWorkLog3 = $I->createBusinessTripWorkLog([
//            'date' => new \DateTimeImmutable(),
//            'timeRejected' => new \DateTimeImmutable(),
//            'workMonth' => $workMonth2,
//        ]);
//        $businessTripWorkLog4 = $I->createBusinessTripWorkLog([
//            'date' => new \DateTimeImmutable(),
//            'workMonth' => $workMonth1,
//        ]);
//
//        $homeOfficeWorkLog1 = $I->createHomeOfficeWorkLog([
//            'date' => new \DateTimeImmutable(),
//            'workMonth' => $workMonth2,
//        ]);
//        $homeOfficeWorkLog2 = $I->createHomeOfficeWorkLog([
//            'date' => new \DateTimeImmutable(),
//            'timeApproved' => new \DateTimeImmutable(),
//            'workMonth' => $workMonth2,
//        ]);
//        $homeOfficeWorkLog3 = $I->createHomeOfficeWorkLog([
//            'date' => new \DateTimeImmutable(),
//            'timeRejected' => new \DateTimeImmutable(),
//            'workMonth' => $workMonth2,
//        ]);
//        $homeOfficeWorkLog4 = $I->createHomeOfficeWorkLog([
//            'date' => new \DateTimeImmutable(),
//            'workMonth' => $workMonth1,
//        ]);
//
//        $overTimeWorkLog1 = $I->createOvertimeWorkLog([
//            'date' => new \DateTimeImmutable(),
//            'workMonth' => $workMonth2,
//        ]);
//        $overTimeWorkLog2 = $I->createOvertimeWorkLog([
//            'date' => new \DateTimeImmutable(),
//            'timeApproved' => new \DateTimeImmutable(),
//            'workMonth' => $workMonth2,
//        ]);
//        $overTimeWorkLog3 = $I->createOvertimeWorkLog([
//            'date' => new \DateTimeImmutable(),
//            'timeRejected' => new \DateTimeImmutable(),
//            'workMonth' => $workMonth2,
//        ]);
//        $overTimeWorkLog4 = $I->createOvertimeWorkLog([
//            'date' => new \DateTimeImmutable(),
//            'workMonth' => $workMonth1,
//        ]);
//
//        $timeOffWorkLog1 = $I->createTimeOffWorkLog([
//            'date' => new \DateTimeImmutable(),
//            'workMonth' => $workMonth2,
//        ]);
//        $timeOffWorkLog2 = $I->createTimeOffWorkLog([
//            'date' => new \DateTimeImmutable(),
//            'timeApproved' => new \DateTimeImmutable(),
//            'workMonth' => $workMonth2,
//        ]);
//        $timeOffWorkLog3 = $I->createTimeOffWorkLog([
//            'date' => new \DateTimeImmutable(),
//            'timeRejected' => new \DateTimeImmutable(),
//            'workMonth' => $workMonth1,
//        ]);
//        $timeOffWorkLog4 = $I->createTimeOffWorkLog([
//            'date' => new \DateTimeImmutable(),
//            'workMonth' => $workMonth1,
//        ]);
//
//        $vacationWorkLog1 = $I->createVacationWorkLog([
//            'date' => new \DateTimeImmutable(),
//            'workMonth' => $workMonth2,
//        ]);
//        $vacationWorkLog2 = $I->createVacationWorkLog([
//            'date' => new \DateTimeImmutable(),
//            'timeApproved' => new \DateTimeImmutable(),
//            'workMonth' => $workMonth2,
//        ]);
//        $vacationWorkLog3 = $I->createVacationWorkLog([
//            'date' => new \DateTimeImmutable(),
//            'timeRejected' => new \DateTimeImmutable(),
//            'workMonth' => $workMonth1,
//        ]);
//        $vacationWorkLog4 = $I->createVacationWorkLog([
//            'date' => new \DateTimeImmutable(),
//            'workMonth' => $workMonth1,
//        ]);
//
//        $I->haveHttpHeader('Content-Type', 'application/json');
//
//        $I->sendGET(sprintf('/special_approvals/%s', $user1->getId()));
//
//        $I->seeHttpHeader('Content-Type', 'application/json');
//        $I->seeResponseCodeIs(Response::HTTP_OK);
//        $I->seeResponseContainsJson([
//            'businessTripWorkLogs' => [
//                [
//                    'date' => $businessTripWorkLog1->getDate()->format(\DateTime::RFC3339),
//                    'id' => $businessTripWorkLog1->getId(),
//                    'timeApproved' => null,
//                    'timeRejected' => null,
//                    'workMonth' => [
//                        'id' => $workMonth2->getId(),
//                        'month' => $workMonth2->getMonth(),
//                        'status' => $workMonth2->getStatus(),
//                        'user' => [
//                            'email' => $user2->getEmail(),
//                            'firstName' => $user2->getFirstName(),
//                            'lastName' => $user2->getLastName(),
//                            'id' => $user2->getId(),
//                        ],
//                        'year' => [
//                            'year' => $workMonth2->getYear()->getYear(),
//                        ],
//                    ],
//                ],
//            ],
//            'homeOfficeWorkLogs' => [
//                [
//                    'date' => $homeOfficeWorkLog1->getDate()->format(\DateTime::RFC3339),
//                    'id' => $homeOfficeWorkLog1->getId(),
//                    'timeApproved' => null,
//                    'timeRejected' => null,
//                    'workMonth' => [
//                        'id' => $workMonth2->getId(),
//                        'month' => $workMonth2->getMonth(),
//                        'status' => $workMonth2->getStatus(),
//                        'user' => [
//                            'email' => $user2->getEmail(),
//                            'firstName' => $user2->getFirstName(),
//                            'lastName' => $user2->getLastName(),
//                            'id' => $user2->getId(),
//                        ],
//                        'year' => [
//                            'year' => $workMonth2->getYear()->getYear(),
//                        ],
//                    ],
//                ],
//            ],
//            'overtimeWorkLogs' => [
//                [
//                    'date' => $overTimeWorkLog1->getDate()->format(\DateTime::RFC3339),
//                    'id' => $overTimeWorkLog1->getId(),
//                    'timeApproved' => null,
//                    'timeRejected' => null,
//                    'workMonth' => [
//                        'id' => $workMonth2->getId(),
//                        'month' => $workMonth2->getMonth(),
//                        'status' => $workMonth2->getStatus(),
//                        'user' => [
//                            'email' => $user2->getEmail(),
//                            'firstName' => $user2->getFirstName(),
//                            'lastName' => $user2->getLastName(),
//                            'id' => $user2->getId(),
//                        ],
//                        'year' => [
//                            'year' => $workMonth2->getYear()->getYear(),
//                        ],
//                    ],
//                ],
//            ],
//            'timeOffWorkLogs' => [
//                [
//                    'date' => $timeOffWorkLog1->getDate()->format(\DateTime::RFC3339),
//                    'id' => $timeOffWorkLog1->getId(),
//                    'timeApproved' => null,
//                    'timeRejected' => null,
//                    'workMonth' => [
//                        'id' => $workMonth2->getId(),
//                        'month' => $workMonth2->getMonth(),
//                        'status' => $workMonth2->getStatus(),
//                        'user' => [
//                            'email' => $user2->getEmail(),
//                            'firstName' => $user2->getFirstName(),
//                            'lastName' => $user2->getLastName(),
//                            'id' => $user2->getId(),
//                        ],
//                        'year' => [
//                            'year' => $workMonth2->getYear()->getYear(),
//                        ],
//                    ],
//                ],
//            ],
//            'vacationWorkLogs' => [
//                [
//                    'date' => $vacationWorkLog1->getDate()->format(\DateTime::RFC3339),
//                    'id' => $vacationWorkLog1->getId(),
//                    'timeApproved' => null,
//                    'timeRejected' => null,
//                    'workMonth' => [
//                        'id' => $workMonth2->getId(),
//                        'month' => $workMonth2->getMonth(),
//                        'status' => $workMonth2->getStatus(),
//                        'user' => [
//                            'email' => $user2->getEmail(),
//                            'firstName' => $user2->getFirstName(),
//                            'lastName' => $user2->getLastName(),
//                            'id' => $user2->getId(),
//                        ],
//                        'year' => [
//                            'year' => $workMonth2->getYear()->getYear(),
//                        ],
//                    ],
//                ],
//            ],
//        ]);
//        $I->dontSeeResponseContainsJson([
//            'businessTripWorkLogs' => [
//                ['id' => $businessTripWorkLog2->getId()],
//                ['id' => $businessTripWorkLog3->getId()],
//                ['id' => $businessTripWorkLog4->getId()],
//            ],
//            'homeOfficeWorkLogs' => [
//                ['id' => $homeOfficeWorkLog2->getId()],
//                ['id' => $homeOfficeWorkLog3->getId()],
//                ['id' => $homeOfficeWorkLog4->getId()],
//            ],
//            'overTimeWorkLogs' => [
//                ['id' => $overTimeWorkLog2->getId()],
//                ['id' => $overTimeWorkLog3->getId()],
//                ['id' => $overTimeWorkLog4->getId()],
//            ],
//            'timeOffWorkLogs' => [
//                ['id' => $timeOffWorkLog2->getId()],
//                ['id' => $timeOffWorkLog3->getId()],
//                ['id' => $timeOffWorkLog4->getId()],
//            ],
//            'vacationWorkLogs' => [
//                ['id' => $vacationWorkLog2->getId()],
//                ['id' => $vacationWorkLog3->getId()],
//                ['id' => $vacationWorkLog4->getId()],
//            ],
//        ]);
//    }

    public function testGetAllAsSuperAdmin(\ApiTester $I)
    {
        $this->beforeSuperAdmin($I);

        $user1 = $I->createUser(['email' => 'user2@example.com', 'employeeId' => 'id123']);
        $user2 = $I->createUser(['email' => 'user1@example.com', 'employeeId' => 'id456']);

        $workMonth1 = $I->createWorkMonth(['user' => $user1]);
        $workMonth2 = $I->createWorkMonth(['user' => $user2]);

        $businessTripWorkLog1 = $I->createBusinessTripWorkLog([
            'date' => new \DateTimeImmutable(),
            'workMonth' => $workMonth2,
        ]);
        $businessTripWorkLog2 = $I->createBusinessTripWorkLog([
            'date' => new \DateTimeImmutable(),
            'timeApproved' => new \DateTimeImmutable(),
            'workMonth' => $workMonth2,
        ]);
        $businessTripWorkLog3 = $I->createBusinessTripWorkLog([
            'date' => new \DateTimeImmutable(),
            'timeRejected' => new \DateTimeImmutable(),
            'workMonth' => $workMonth2,
        ]);
        $businessTripWorkLog4 = $I->createBusinessTripWorkLog([
            'date' => new \DateTimeImmutable(),
            'workMonth' => $workMonth1,
        ]);

        $homeOfficeWorkLog1 = $I->createHomeOfficeWorkLog([
            'date' => new \DateTimeImmutable(),
            'workMonth' => $workMonth2,
        ]);
        $homeOfficeWorkLog2 = $I->createHomeOfficeWorkLog([
            'date' => new \DateTimeImmutable(),
            'timeApproved' => new \DateTimeImmutable(),
            'workMonth' => $workMonth2,
        ]);
        $homeOfficeWorkLog3 = $I->createHomeOfficeWorkLog([
            'date' => new \DateTimeImmutable(),
            'timeRejected' => new \DateTimeImmutable(),
            'workMonth' => $workMonth2,
        ]);
        $homeOfficeWorkLog4 = $I->createHomeOfficeWorkLog([
            'date' => new \DateTimeImmutable(),
            'workMonth' => $workMonth1,
        ]);

        $overTimeWorkLog1 = $I->createOvertimeWorkLog([
            'date' => new \DateTimeImmutable(),
            'workMonth' => $workMonth2,
        ]);
        $overTimeWorkLog2 = $I->createOvertimeWorkLog([
            'date' => new \DateTimeImmutable(),
            'timeApproved' => new \DateTimeImmutable(),
            'workMonth' => $workMonth2,
        ]);
        $overTimeWorkLog3 = $I->createOvertimeWorkLog([
            'date' => new \DateTimeImmutable(),
            'timeRejected' => new \DateTimeImmutable(),
            'workMonth' => $workMonth2,
        ]);
        $overTimeWorkLog4 = $I->createOvertimeWorkLog([
            'date' => new \DateTimeImmutable(),
            'workMonth' => $workMonth1,
        ]);

        $specialLeaveWorkLog1 = $I->createSpecialLeaveWorkLog([
            'date' => new \DateTimeImmutable(),
            'workMonth' => $workMonth2,
        ]);
        $specialLeaveWorkLog2 = $I->createSpecialLeaveWorkLog([
            'date' => new \DateTimeImmutable(),
            'timeApproved' => new \DateTimeImmutable(),
            'workMonth' => $workMonth2,
        ]);
        $specialLeaveWorkLog3 = $I->createSpecialLeaveWorkLog([
            'date' => new \DateTimeImmutable(),
            'timeRejected' => new \DateTimeImmutable(),
            'workMonth' => $workMonth1,
        ]);
        $specialLeaveWorkLog4 = $I->createSpecialLeaveWorkLog([
            'date' => new \DateTimeImmutable(),
            'workMonth' => $workMonth1,
        ]);

        $timeOffWorkLog1 = $I->createTimeOffWorkLog([
            'date' => new \DateTimeImmutable(),
            'workMonth' => $workMonth2,
        ]);
        $timeOffWorkLog2 = $I->createTimeOffWorkLog([
            'date' => new \DateTimeImmutable(),
            'timeApproved' => new \DateTimeImmutable(),
            'workMonth' => $workMonth2,
        ]);
        $timeOffWorkLog3 = $I->createTimeOffWorkLog([
            'date' => new \DateTimeImmutable(),
            'timeRejected' => new \DateTimeImmutable(),
            'workMonth' => $workMonth1,
        ]);
        $timeOffWorkLog4 = $I->createTimeOffWorkLog([
            'date' => new \DateTimeImmutable(),
            'workMonth' => $workMonth1,
        ]);

        $trainingWorkLog1 = $I->createTrainingWorkLog([
            'date' => new \DateTimeImmutable(),
            'workMonth' => $workMonth2,
        ]);
        $trainingWorkLog2 = $I->createTrainingWorkLog([
            'date' => new \DateTimeImmutable(),
            'timeApproved' => new \DateTimeImmutable(),
            'workMonth' => $workMonth2,
        ]);
        $trainingWorkLog3 = $I->createTrainingWorkLog([
            'date' => new \DateTimeImmutable(),
            'timeRejected' => new \DateTimeImmutable(),
            'workMonth' => $workMonth2,
        ]);
        $trainingWorkLog4 = $I->createTrainingWorkLog([
            'date' => new \DateTimeImmutable(),
            'workMonth' => $workMonth1,
        ]);

        $vacationWorkLog1 = $I->createVacationWorkLog([
            'date' => new \DateTimeImmutable(),
            'workMonth' => $workMonth2,
        ]);
        $vacationWorkLog2 = $I->createVacationWorkLog([
            'date' => new \DateTimeImmutable(),
            'timeApproved' => new \DateTimeImmutable(),
            'workMonth' => $workMonth2,
        ]);
        $vacationWorkLog3 = $I->createVacationWorkLog([
            'date' => new \DateTimeImmutable(),
            'timeRejected' => new \DateTimeImmutable(),
            'workMonth' => $workMonth1,
        ]);
        $vacationWorkLog4 = $I->createVacationWorkLog([
            'date' => new \DateTimeImmutable(),
            'workMonth' => $workMonth1,
        ]);

        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendGET(sprintf('/special_approvals/%s', $user1->getId()));

        $I->seeHttpHeader('Content-Type', 'application/json');
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseContainsJson([
            'businessTripWorkLogs' => [
                [
                    'date' => $businessTripWorkLog1->getDate()->format(\DateTime::RFC3339),
                    'id' => $businessTripWorkLog1->getId(),
                    'plannedEndHour' => 23,
                    'plannedEndMinute' => 59,
                    'plannedStartHour' => 0,
                    'plannedStartMinute' => 0,
                    'timeApproved' => null,
                    'timeRejected' => null,
                    'workMonth' => [
                        'id' => $workMonth2->getId(),
                        'month' => $workMonth2->getMonth(),
                        'status' => $workMonth2->getStatus(),
                        'user' => [
                            'email' => $user2->getEmail(),
                            'firstName' => $user2->getFirstName(),
                            'lastName' => $user2->getLastName(),
                            'id' => $user2->getId(),
                        ],
                        'year' => [
                            'year' => $workMonth2->getYear()->getYear(),
                        ],
                    ],
                ],
                [
                    'date' => $businessTripWorkLog4->getDate()->format(\DateTime::RFC3339),
                    'id' => $businessTripWorkLog4->getId(),
                    'timeApproved' => null,
                    'timeRejected' => null,
                    'workMonth' => [
                        'id' => $workMonth1->getId(),
                        'month' => $workMonth1->getMonth(),
                        'status' => $workMonth1->getStatus(),
                        'user' => [
                            'email' => $user1->getEmail(),
                            'firstName' => $user1->getFirstName(),
                            'lastName' => $user1->getLastName(),
                            'id' => $user1->getId(),
                        ],
                        'year' => [
                            'year' => $workMonth1->getYear()->getYear(),
                        ],
                    ],
                ],
            ],
            'homeOfficeWorkLogs' => [
                [
                    'date' => $homeOfficeWorkLog1->getDate()->format(\DateTime::RFC3339),
                    'id' => $homeOfficeWorkLog1->getId(),
                    'timeApproved' => null,
                    'timeRejected' => null,
                    'workMonth' => [
                        'id' => $workMonth2->getId(),
                        'month' => $workMonth2->getMonth(),
                        'status' => $workMonth2->getStatus(),
                        'user' => [
                            'email' => $user2->getEmail(),
                            'firstName' => $user2->getFirstName(),
                            'lastName' => $user2->getLastName(),
                            'id' => $user2->getId(),
                        ],
                        'year' => [
                            'year' => $workMonth2->getYear()->getYear(),
                        ],
                    ],
                ],
                [
                    'date' => $homeOfficeWorkLog4->getDate()->format(\DateTime::RFC3339),
                    'id' => $homeOfficeWorkLog4->getId(),
                    'timeApproved' => null,
                    'timeRejected' => null,
                    'workMonth' => [
                        'id' => $workMonth1->getId(),
                        'month' => $workMonth1->getMonth(),
                        'status' => $workMonth1->getStatus(),
                        'user' => [
                            'email' => $user1->getEmail(),
                            'firstName' => $user1->getFirstName(),
                            'lastName' => $user1->getLastName(),
                            'id' => $user1->getId(),
                        ],
                        'year' => [
                            'year' => $workMonth1->getYear()->getYear(),
                        ],
                    ],
                ],
            ],
            'overtimeWorkLogs' => [
                [
                    'date' => $overTimeWorkLog1->getDate()->format(\DateTime::RFC3339),
                    'id' => $overTimeWorkLog1->getId(),
                    'timeApproved' => null,
                    'timeRejected' => null,
                    'workMonth' => [
                        'id' => $workMonth2->getId(),
                        'month' => $workMonth2->getMonth(),
                        'status' => $workMonth2->getStatus(),
                        'user' => [
                            'email' => $user2->getEmail(),
                            'firstName' => $user2->getFirstName(),
                            'lastName' => $user2->getLastName(),
                            'id' => $user2->getId(),
                        ],
                        'year' => [
                            'year' => $workMonth2->getYear()->getYear(),
                        ],
                    ],
                ],
                [
                    'date' => $overTimeWorkLog4->getDate()->format(\DateTime::RFC3339),
                    'id' => $overTimeWorkLog4->getId(),
                    'timeApproved' => null,
                    'timeRejected' => null,
                    'workMonth' => [
                        'id' => $workMonth1->getId(),
                        'month' => $workMonth1->getMonth(),
                        'status' => $workMonth1->getStatus(),
                        'user' => [
                            'email' => $user1->getEmail(),
                            'firstName' => $user1->getFirstName(),
                            'lastName' => $user1->getLastName(),
                            'id' => $user1->getId(),
                        ],
                        'year' => [
                            'year' => $workMonth1->getYear()->getYear(),
                        ],
                    ],
                ],
            ],
            'specialLeaveWorkLogs' => [
                [
                    'date' => $specialLeaveWorkLog1->getDate()->format(\DateTime::RFC3339),
                    'id' => $specialLeaveWorkLog1->getId(),
                    'timeApproved' => null,
                    'timeRejected' => null,
                    'workMonth' => [
                        'id' => $workMonth2->getId(),
                        'month' => $workMonth2->getMonth(),
                        'status' => $workMonth2->getStatus(),
                        'user' => [
                            'email' => $user2->getEmail(),
                            'firstName' => $user2->getFirstName(),
                            'lastName' => $user2->getLastName(),
                            'id' => $user2->getId(),
                        ],
                        'year' => [
                            'year' => $workMonth2->getYear()->getYear(),
                        ],
                    ],
                ],
                [
                    'date' => $specialLeaveWorkLog4->getDate()->format(\DateTime::RFC3339),
                    'id' => $specialLeaveWorkLog4->getId(),
                    'timeApproved' => null,
                    'timeRejected' => null,
                    'workMonth' => [
                        'id' => $workMonth1->getId(),
                        'month' => $workMonth1->getMonth(),
                        'status' => $workMonth1->getStatus(),
                        'user' => [
                            'email' => $user1->getEmail(),
                            'firstName' => $user1->getFirstName(),
                            'lastName' => $user1->getLastName(),
                            'id' => $user1->getId(),
                        ],
                        'year' => [
                            'year' => $workMonth1->getYear()->getYear(),
                        ],
                    ],
                ],
            ],
            'timeOffWorkLogs' => [
                [
                    'date' => $timeOffWorkLog1->getDate()->format(\DateTime::RFC3339),
                    'id' => $timeOffWorkLog1->getId(),
                    'timeApproved' => null,
                    'timeRejected' => null,
                    'workMonth' => [
                        'id' => $workMonth2->getId(),
                        'month' => $workMonth2->getMonth(),
                        'status' => $workMonth2->getStatus(),
                        'user' => [
                            'email' => $user2->getEmail(),
                            'firstName' => $user2->getFirstName(),
                            'lastName' => $user2->getLastName(),
                            'id' => $user2->getId(),
                        ],
                        'year' => [
                            'year' => $workMonth2->getYear()->getYear(),
                        ],
                    ],
                ],
                [
                    'date' => $timeOffWorkLog4->getDate()->format(\DateTime::RFC3339),
                    'id' => $timeOffWorkLog4->getId(),
                    'timeApproved' => null,
                    'timeRejected' => null,
                    'workMonth' => [
                        'id' => $workMonth1->getId(),
                        'month' => $workMonth1->getMonth(),
                        'status' => $workMonth1->getStatus(),
                        'user' => [
                            'email' => $user1->getEmail(),
                            'firstName' => $user1->getFirstName(),
                            'lastName' => $user1->getLastName(),
                            'id' => $user1->getId(),
                        ],
                        'year' => [
                            'year' => $workMonth1->getYear()->getYear(),
                        ],
                    ],
                ],
            ],
            'trainingWorkLogs' => [
                [
                    'date' => $trainingWorkLog1->getDate()->format(\DateTime::RFC3339),
                    'id' => $trainingWorkLog1->getId(),
                    'timeApproved' => null,
                    'timeRejected' => null,
                    'workMonth' => [
                        'id' => $workMonth2->getId(),
                        'month' => $workMonth2->getMonth(),
                        'status' => $workMonth2->getStatus(),
                        'user' => [
                            'email' => $user2->getEmail(),
                            'firstName' => $user2->getFirstName(),
                            'lastName' => $user2->getLastName(),
                            'id' => $user2->getId(),
                        ],
                        'year' => [
                            'year' => $workMonth2->getYear()->getYear(),
                        ],
                    ],
                ],
                [
                    'date' => $trainingWorkLog4->getDate()->format(\DateTime::RFC3339),
                    'id' => $trainingWorkLog4->getId(),
                    'timeApproved' => null,
                    'timeRejected' => null,
                    'workMonth' => [
                        'id' => $workMonth1->getId(),
                        'month' => $workMonth1->getMonth(),
                        'status' => $workMonth1->getStatus(),
                        'user' => [
                            'email' => $user1->getEmail(),
                            'firstName' => $user1->getFirstName(),
                            'lastName' => $user1->getLastName(),
                            'id' => $user1->getId(),
                        ],
                        'year' => [
                            'year' => $workMonth1->getYear()->getYear(),
                        ],
                    ],
                ],
            ],
            'vacationWorkLogs' => [
                [
                    'date' => $vacationWorkLog1->getDate()->format(\DateTime::RFC3339),
                    'id' => $vacationWorkLog1->getId(),
                    'timeApproved' => null,
                    'timeRejected' => null,
                    'workMonth' => [
                        'id' => $workMonth2->getId(),
                        'month' => $workMonth2->getMonth(),
                        'status' => $workMonth2->getStatus(),
                        'user' => [
                            'email' => $user2->getEmail(),
                            'firstName' => $user2->getFirstName(),
                            'lastName' => $user2->getLastName(),
                            'id' => $user2->getId(),
                        ],
                        'year' => [
                            'year' => $workMonth2->getYear()->getYear(),
                        ],
                    ],
                ],
                [
                    'date' => $vacationWorkLog4->getDate()->format(\DateTime::RFC3339),
                    'id' => $vacationWorkLog4->getId(),
                    'timeApproved' => null,
                    'timeRejected' => null,
                    'workMonth' => [
                        'id' => $workMonth1->getId(),
                        'month' => $workMonth1->getMonth(),
                        'status' => $workMonth1->getStatus(),
                        'user' => [
                            'email' => $user1->getEmail(),
                            'firstName' => $user1->getFirstName(),
                            'lastName' => $user1->getLastName(),
                            'id' => $user1->getId(),
                        ],
                        'year' => [
                            'year' => $workMonth1->getYear()->getYear(),
                        ],
                    ],
                ],
            ],
        ]);
        $I->dontSeeResponseContainsJson([
            'businessTripWorkLogs' => [
                ['id' => $businessTripWorkLog2->getId()],
                ['id' => $businessTripWorkLog3->getId()],
            ],
            'homeOfficeWorkLogs' => [
                ['id' => $homeOfficeWorkLog2->getId()],
                ['id' => $homeOfficeWorkLog3->getId()],
            ],
            'overTimeWorkLogs' => [
                ['id' => $overTimeWorkLog2->getId()],
                ['id' => $overTimeWorkLog3->getId()],
            ],
            'specialLeaveWorkLogs' => [
                ['id' => $specialLeaveWorkLog2->getId()],
                ['id' => $specialLeaveWorkLog3->getId()],
            ],
            'timeOffWorkLogs' => [
                ['id' => $timeOffWorkLog2->getId()],
                ['id' => $timeOffWorkLog3->getId()],
            ],
            'trainingWorkLogs' => [
                ['id' => $trainingWorkLog2->getId()],
                ['id' => $trainingWorkLog3->getId()],
            ],
            'vacationWorkLogs' => [
                ['id' => $vacationWorkLog2->getId()],
                ['id' => $vacationWorkLog3->getId()],
            ],
        ]);
    }
}
