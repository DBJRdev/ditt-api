<?php

namespace App\Event;

use App\Entity\OvertimeWorkLog;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class OvertimeWorkLogCanceledEvent extends Event
{
    const CANCELED = 'event.overtime_work_log.canceled';

    /**
     * @var OvertimeWorkLog
     */
    private $overtimeWorkLog;
    /**
     * @var User|null
     */
    private $supervisor;

    public function __construct(OvertimeWorkLog $overtimeWorkLog, ?User $supervisor)
    {
        $this->overtimeWorkLog = $overtimeWorkLog;
        $this->supervisor = $supervisor;
    }

    public function getOvertimeWorkLog(): OvertimeWorkLog
    {
        return $this->overtimeWorkLog;
    }

    public function getSupervisor(): ?User
    {
        return $this->supervisor;
    }
}
