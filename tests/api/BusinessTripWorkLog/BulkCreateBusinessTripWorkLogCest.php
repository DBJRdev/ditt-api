<?php

namespace api\BusinessTripWorkLog;

use App\Entity\BusinessTripWorkLog;
use App\Entity\User;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\Response;

class BulkCreateBusinessTripWorkLogCest
{
    /**
     * @var User
     */
    private $user;

    public function _before(\ApiTester $I)
    {
        $this->user = $I->createUser();
        $I->login($this->user);
    }

    /**
     * @throws \Exception
     */
    public function testBulkCreateWithValidData(\ApiTester $I): void
    {
        $date = new \DateTimeImmutable('2019-06-01T12:00:00');
        $date2 = $date->add(new \DateInterval('P1D'));
        $I->createWorkMonth([
            'month' => $date->format('m'),
            'user' => $this->user,
            'year' => $I->getSupportedYear($date->format('Y')),
        ]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/business_trip_work_logs/bulk', [
            [
                'date' => $date->format(\DateTime::RFC3339),
                'purpose' => 'Event 1',
                'destination' => 'Prague',
                'transport' => 'Plane',
                'expectedDeparture' => 'At the morning',
                'expectedArrival' => 'In the evening',
                'plannedEndHour' => 23,
                'plannedEndMinute' => 59,
                'plannedStartHour' => 0,
                'plannedStartMinute' => 0,
            ],
            [
                'date' => $date2->format(\DateTime::RFC3339),
                'purpose' => 'Event 2',
                'destination' => 'Prague',
                'transport' => 'Plane',
                'expectedDeparture' => 'At the morning',
                'expectedArrival' => 'In the evening',
                'plannedEndHour' => 23,
                'plannedEndMinute' => 59,
                'plannedStartHour' => 0,
                'plannedStartMinute' => 0,
            ],
        ]);

        $I->seeHttpHeader('Content-Type', 'application/json');
        $I->seeResponseCodeIs(Response::HTTP_CREATED);
        $I->seeResponseContainsJson([
            [
                'date' => $date->format(\DateTime::RFC3339),
                'purpose' => 'Event 1',
                'destination' => 'Prague',
                'transport' => 'Plane',
                'expectedDeparture' => 'At the morning',
                'expectedArrival' => 'In the evening',
                'plannedEndHour' => 23,
                'plannedEndMinute' => 59,
                'plannedStartHour' => 0,
                'plannedStartMinute' => 0,
            ],
            [
                'date' => $date2->format(\DateTime::RFC3339),
                'purpose' => 'Event 2',
                'destination' => 'Prague',
                'transport' => 'Plane',
                'expectedDeparture' => 'At the morning',
                'expectedArrival' => 'In the evening',
                'plannedEndHour' => 23,
                'plannedEndMinute' => 59,
                'plannedStartHour' => 0,
                'plannedStartMinute' => 0,
            ],
        ]);
        $I->grabEntityFromRepository(BusinessTripWorkLog::class, [
            'date' => $date,
        ]);
        $I->grabEntityFromRepository(BusinessTripWorkLog::class, [
            'date' => $date2,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function testCreateWithClosedMonth(\ApiTester $I): void
    {
        $date = new \DateTimeImmutable('2019-06-01T12:00:00');
        $I->createWorkMonth([
            'month' => $date->format('m'),
            'status' => 'APPROVED',
            'user' => $this->user,
            'year' => $I->getSupportedYear($date->format('Y')),
        ]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/business_trip_work_logs/bulk', [
            ['date' => $date->format(\DateTime::RFC3339)],
        ]);

        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseContainsJson([
            'detail' => 'Cannot add work log to closed work month.',
        ]);
        $I->expectThrowable(NoResultException::class, function () use ($I, $date) {
            $I->grabEntityFromRepository(BusinessTripWorkLog::class, [
                'date' => $date,
            ]);
        });
    }

    /**
     * @throws \Exception
     */
    public function testCreateWithInvalidData(\ApiTester $I): void
    {
        $date = new \DateTimeImmutable('2019-06-01T12:00:00');
        $I->createWorkMonth([
            'month' => $date->format('m'),
            'user' => $this->user,
            'year' => $I->getSupportedYear($date->format('Y')),
        ]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/business_trip_work_logs/bulk', [
            ['date' => null],
        ]);

        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseContainsJson([
            'detail' => 'Cannot denormalize work log.',
        ]);

        $I->expectThrowable(NoResultException::class, function () use ($I, $date) {
            $I->grabEntityFromRepository(BusinessTripWorkLog::class, [
                'date' => $date,
            ]);
        });
    }
}
