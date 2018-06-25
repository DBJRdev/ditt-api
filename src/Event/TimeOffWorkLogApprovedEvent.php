<?php

namespace App\Event;

use App\Entity\TimeOffWorkLog;
use App\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class TimeOffWorkLogApprovedEvent extends Event
{
    const APPROVED = 'event.time_off_work_log.approved';

    /**
     * @var TimeOffWorkLog
     */
    private $timeOffWorkLog;

    /**
     * @var User
     */
    private $supervisor;

    /**
     * @param TimeOffWorkLog $timeOffWorkLog
     * @param User $supervisor
     */
    public function __construct(TimeOffWorkLog $timeOffWorkLog, User $supervisor)
    {
        $this->timeOffWorkLog = $timeOffWorkLog;
        $this->supervisor = $supervisor;
    }

    /**
     * @return TimeOffWorkLog
     */
    public function getTimeOffWorkLog(): TimeOffWorkLog
    {
        return $this->timeOffWorkLog;
    }

    /**
     * @return User
     */
    public function getSupervisor(): User
    {
        return $this->supervisor;
    }
}