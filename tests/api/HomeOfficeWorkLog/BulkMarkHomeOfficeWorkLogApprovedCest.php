<?php

namespace api\HomeOfficeWorkLog;

use App\Entity\HomeOfficeWorkLog;
use Symfony\Component\HttpFoundation\Response;

class BulkMarkHomeOfficeWorkLogApprovedCest
{
    /**
     * @var User|null
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
    public function testMarkApproved(\ApiTester $I): void
    {
        $user = $I->createUser(['email' => 'user2@example.com', 'employeeId' => '123', 'supervisor' => $this->user]);
        $workMonth = $I->createWorkMonth(['user' => $user]);
        $workLog = $I->createHomeOfficeWorkLog([
            'workMonth' => $workMonth,
        ]);
        $workLog2 = $I->createHomeOfficeWorkLog([
            'workMonth' => $workMonth,
        ]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT(
            '/home_office_work_logs/bulk/mark_approved',
            ['workLogIds' => [$workLog->getId(), $workLog2->getId()]]
        );

        // $I->seeEmailIsSent(1);

        $I->seeHttpHeader('Content-Type', 'application/json');
        $I->seeResponseCodeIs(Response::HTTP_OK);
        $I->seeResponseContainsJson([
            ['id' => $workLog->getId()],
            ['id' => $workLog2->getId()],
        ]);
        $workLog = $I->grabEntityFromRepository(HomeOfficeWorkLog::class, [
            'id' => $workLog->getId(),
        ]);
        $workLog2 = $I->grabEntityFromRepository(HomeOfficeWorkLog::class, [
            'id' => $workLog2->getId(),
        ]);
        $I->assertNotNull($workLog->getTimeApproved());
        $I->assertNull($workLog->getTimeRejected());
        $I->assertNotNull($workLog2->getTimeApproved());
        $I->assertNull($workLog2->getTimeRejected());
    }

    /**
     * @throws \Exception
     */
    public function testAlreadyMarkedApproved(\ApiTester $I): void
    {
        $time = (new \DateTimeImmutable());
        $user = $I->createUser(['email' => 'user2@example.com', 'employeeId' => '123', 'supervisor' => $this->user]);
        $workMonth = $I->createWorkMonth(['user' => $user]);
        $workLog = $I->createHomeOfficeWorkLog([
            'timeApproved' => $time,
            'workMonth' => $workMonth,
        ]);
        $workLog2 = $I->createHomeOfficeWorkLog([
            'workMonth' => $workMonth,
        ]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT(
            '/home_office_work_logs/bulk/mark_approved',
            ['workLogIds' => [$workLog->getId(), $workLog2->getId()]]
        );

        $I->seeHttpHeader('Content-Type', 'application/json');
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseContainsJson([
            'detail' => sprintf('Work log with id %d has been already approved.', $workLog->getId()),
        ]);
        $I->grabEntityFromRepository(HomeOfficeWorkLog::class, [
            'id' => $workLog->getId(),
        ]);
        $I->assertNotNull($workLog->getTimeApproved());
        $I->assertNull($workLog->getTimeRejected());
    }

    /**
     * @throws \Exception
     */
    public function testAlreadyMarkedRejected(\ApiTester $I): void
    {
        $time = (new \DateTimeImmutable());
        $user = $I->createUser(['email' => 'user2@example.com', 'employeeId' => '123', 'supervisor' => $this->user]);
        $workMonth = $I->createWorkMonth(['user' => $user]);
        $workLog = $I->createHomeOfficeWorkLog([
            'timeRejected' => $time,
            'workMonth' => $workMonth,
        ]);
        $workLog2 = $I->createHomeOfficeWorkLog([
            'workMonth' => $workMonth,
        ]);

        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT(
            '/home_office_work_logs/bulk/mark_approved',
            ['workLogIds' => [$workLog->getId(), $workLog2->getId()]]
        );

        $I->seeHttpHeader('Content-Type', 'application/json');
        $I->seeResponseCodeIs(Response::HTTP_BAD_REQUEST);
        $I->seeResponseContainsJson([
            'detail' => sprintf('Work log with id %d has been already rejected.', $workLog->getId()),
        ]);
        $I->grabEntityFromRepository(HomeOfficeWorkLog::class, [
            'id' => $workLog->getId(),
        ]);
        $I->assertNull($workLog->getTimeApproved());
        $I->assertNotNull($workLog->getTimeRejected());
    }
}
