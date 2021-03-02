<?php

namespace api\BusinessTripWorkLog;

use App\Entity\BusinessTripWorkLog;
use App\Entity\User;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\Response;

class CreateBusinessTripWorkLogCest
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
    public function testCreateWithValidData(\ApiTester $I): void
    {
        $date = new \DateTimeImmutable('2019-06-01T12:00:00');
        $I->createWorkMonth([
            'month' => $date->format('m'),
            'user' => $this->user,
            'year' => $I->getSupportedYear($date->format('Y')),
        ]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPOST('/business_trip_work_logs.json', [
            'date' => $date->format(\DateTime::RFC3339),
            'purpose' => 'Event',
            'destination' => 'Prague',
            'transport' => 'Plane',
            'expectedDeparture' => 'At the morning',
            'expectedArrival' => 'In the evening',
        ]);

        $I->seeHttpHeader('Content-Type', 'application/json; charset=utf-8');
        $I->seeResponseCodeIs(Response::HTTP_CREATED);
        $I->seeResponseContainsJson([
            'date' => $date->format(\DateTime::RFC3339),
            'purpose' => 'Event',
            'destination' => 'Prague',
            'transport' => 'Plane',
            'expectedDeparture' => 'At the morning',
            'expectedArrival' => 'In the evening',
        ]);
        $I->grabEntityFromRepository(BusinessTripWorkLog::class, [
            'date' => $date,
            'purpose' => 'Event',
            'destination' => 'Prague',
            'transport' => 'Plane',
            'expectedDeparture' => 'At the morning',
            'expectedArrival' => 'In the evening',
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
        $I->sendPOST('/business_trip_work_logs.json', [
            'date' => $date->format(\DateTime::RFC3339),
            'purpose' => 'Event',
            'destination' => 'Prague',
            'transport' => 'Plane',
            'expectedDeparture' => 'At the morning',
            'expectedArrival' => 'In the evening',
        ]);

        $I->seeHttpHeader('Content-Type', 'application/problem+json; charset=utf-8');
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseContainsJson([
            'detail' => 'Cannot add or delete work log to closed work month.',
        ]);
        $I->expectThrowable(NoResultException::class, function () use ($I, $date) {
            $I->grabEntityFromRepository(BusinessTripWorkLog::class, [
                'date' => $date,
                'purpose' => 'Event',
                'destination' => 'Prague',
                'transport' => 'Plane',
                'expectedDeparture' => 'At the morning',
                'expectedArrival' => 'In the evening',
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
        $I->sendPOST('/business_trip_work_logs.json', [
            'date' => null,
            'purpose' => '',
            'destination' => '',
            'transport' => '',
            'expectedDeparture' => '',
            'expectedArrival' => '',
        ]);

        $I->seeHttpHeader('Content-Type', 'application/problem+json; charset=utf-8');
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseContainsJson([
            'detail' => 'The data is either an empty string or null, you should pass a string '
            . 'that can be parsed with the passed format or a valid DateTime string.',
        ]);

        $I->expectThrowable(NoResultException::class, function () use ($I, $date) {
            $I->grabEntityFromRepository(BusinessTripWorkLog::class, [
                'date' => $date,
            ]);
        });
    }
}
