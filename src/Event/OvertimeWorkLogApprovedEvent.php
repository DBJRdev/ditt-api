<?php

namespace App\Event;

use App\Entity\OvertimeWorkLog;
use App\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class OvertimeWorkLogApprovedEvent extends Event
{
    const APPROVED = 'event.overtime_work_log.approved';

    /**
     * @var OvertimeWorkLog
     */
    private $overtimeWorkLog;

    /**
     * @var User
     */
    private $supervisor;

    /**
     * @param OvertimeWorkLog $overtimeWorkLog
     * @param User $supervisor
     */
    public function __construct(OvertimeWorkLog $overtimeWorkLog, User $supervisor)
    {
        $this->overtimeWorkLog = $overtimeWorkLog;
        $this->supervisor = $supervisor;
    }

    /**
     * @return OvertimeWorkLog
     */
    public function getOvertimeWorkLog(): OvertimeWorkLog
    {
        return $this->overtimeWorkLog;
    }

    /**
     * @return User
     */
    public function getSupervisor(): User
    {
        return $this->supervisor;
    }
}
