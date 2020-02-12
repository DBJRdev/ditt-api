<?php

namespace App\Service;

use App\Entity\BusinessTripWorkLog;
use App\Entity\HomeOfficeWorkLog;
use App\Entity\SickDayWorkLog;
use App\Entity\TimeOffWorkLog;
use App\Entity\VacationWorkLog;
use App\Entity\WorkLog;
use App\Entity\WorkMonth;
use App\Repository\BusinessTripWorkLogRepository;
use App\Repository\HomeOfficeWorkLogRepository;
use App\Repository\SickDayWorkLogRepository;
use App\Repository\TimeOffWorkLogRepository;
use App\Repository\UserYearStatsRepository;
use App\Repository\VacationWorkLogRepository;
use App\Repository\WorkHoursRepository;
use App\Repository\WorkLogRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class WorkMonthService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ConfigService
     */
    private $configService;

    /**
     * @var BusinessTripWorkLogRepository
     */
    private $businessTripWorkLogRepository;

    /**
     * @var HomeOfficeWorkLogRepository
     */
    private $homeOfficeWorkLogRepository;

    /**
     * @var SickDayWorkLogRepository
     */
    private $sickDayWorkLogRepository;

    /**
     * @var TimeOffWorkLogRepository
     */
    private $timeOffWorkLogRepository;

    /**
     * @var UserYearStatsRepository
     */
    private $userYearStatsRepository;

    /**
     * @var VacationWorkLogRepository
     */
    private $vacationWorkLogRepository;

    /**
     * @var WorkLogRepository
     */
    private $workLogRepository;

    /**
     * @var WorkHoursRepository
     */
    private $workHoursRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ConfigService $configService,
        BusinessTripWorkLogRepository $businessTripWorkLogRepository,
        HomeOfficeWorkLogRepository $homeOfficeWorkLogRepository,
        SickDayWorkLogRepository $sickDayWorkLogRepository,
        TimeOffWorkLogRepository $timeOffWorkLogRepository,
        UserYearStatsRepository $userYearStatsRepository,
        VacationWorkLogRepository $vacationWorkLogRepository,
        WorkLogRepository $workLogRepository,
        WorkHoursRepository $workHoursRepository
    ) {
        $this->entityManager = $entityManager;
        $this->configService = $configService;
        $this->businessTripWorkLogRepository = $businessTripWorkLogRepository;
        $this->homeOfficeWorkLogRepository = $homeOfficeWorkLogRepository;
        $this->sickDayWorkLogRepository = $sickDayWorkLogRepository;
        $this->timeOffWorkLogRepository = $timeOffWorkLogRepository;
        $this->userYearStatsRepository = $userYearStatsRepository;
        $this->vacationWorkLogRepository = $vacationWorkLogRepository;
        $this->workLogRepository = $workLogRepository;
        $this->workHoursRepository = $workHoursRepository;
    }

    /**
     * @param WorkMonth[] $workMonths
     */
    public function createWorkMonths(array $workMonths): void
    {
        $this->entityManager->transactional(function (EntityManager $em) use ($workMonths) {
            foreach ($workMonths as $workMonth) {
                if (!$workMonth instanceof WorkMonth) {
                    throw new \TypeError('Entity is not of type WorkMonth.');
                }

                $em->persist($workMonth);
            }
        });
    }

    /**
     * @throws \Exception
     */
    public function markApproved(WorkMonth $workMonth): void
    {
        $workMonth->markApproved();

        $userYearStats = $this->userYearStatsRepository->findByUserAndYear(
            $workMonth->getUser(),
            $workMonth->getYear()
        );

        if (!$userYearStats) {
            throw new \Exception('User year stats has not been found.');
        }

        $userYearStats->setRequiredHours($userYearStats->getRequiredHours() + $this->calculateRequiredHours($workMonth));
        $userYearStats->setWorkedHours($userYearStats->getWorkedHours() + $this->calculateWorkedHours($workMonth));

        $this->entityManager->flush();
    }

    public function markWaitingForApproval(WorkMonth $workMonth): void
    {
        $workMonth->markWaitingForApproval();
        $this->entityManager->flush();
    }

    /**
     * @throws \Exception
     */
    public function calculateWorkedHours(WorkMonth $workMonth): float
    {
        $workHours = $this->workHoursRepository->findOne(
            $workMonth->getYear(),
            $workMonth->getMonth(),
            $workMonth->getUser()
        );

        if (!$workHours) {
            throw new \Exception('Work hours has not been found.');
        }

        $allWorkLogs = [];
        $allWorkLogWorkTime = [];

        for ($day = 1; $day < 32; ++$day) {
            $allWorkLogs[$day] = [];
            $allWorkLogWorkTime[$day] = 0;
        }

        $standardWorkLogs = $this->workLogRepository->findAllByWorkMonth($workMonth);
        $businessTripWorkLogs = $this->businessTripWorkLogRepository->findAllApprovedByWorkMonth($workMonth);
        $homeOfficeWorkLogs = $this->homeOfficeWorkLogRepository->findAllApprovedByWorkMonth($workMonth);
        $sickDayWorkLogs = $this->sickDayWorkLogRepository->findAllByWorkMonth($workMonth);
        $timeOffWorkLogs = $this->timeOffWorkLogRepository->findAllApprovedByWorkMonth($workMonth);
        $vacationWorkLogs = $this->vacationWorkLogRepository->findAllApprovedByWorkMonth($workMonth);

        foreach ($standardWorkLogs as $standardWorkLog) {
            $day = (int) $standardWorkLog->getStartTime()->format('d');
            $allWorkLogs[$day][] = $standardWorkLog;
        }

        foreach ($businessTripWorkLogs as $businessTripWorkLog) {
            $day = (int) $businessTripWorkLog->getDate()->format('d');
            $allWorkLogs[$day][] = $businessTripWorkLog;
        }

        foreach ($homeOfficeWorkLogs as $homeOfficeWorkLog) {
            $day = (int) $homeOfficeWorkLog->getDate()->format('d');
            $allWorkLogs[$day][] = $homeOfficeWorkLog;
        }

        foreach ($sickDayWorkLogs as $sickDayWorkLog) {
            $day = (int) $sickDayWorkLog->getDate()->format('d');
            $allWorkLogs[$day][] = $sickDayWorkLog;
        }

        foreach ($timeOffWorkLogs as $timeOffWorkLog) {
            $day = (int) $timeOffWorkLog->getDate()->format('d');
            $allWorkLogs[$day][] = $timeOffWorkLog;
        }

        foreach ($vacationWorkLogs as $vacationWorkLog) {
            $day = (int) $vacationWorkLog->getDate()->format('d');
            $allWorkLogs[$day][] = $vacationWorkLog;
        }

        // Calculate work time for each separately
        foreach ($allWorkLogs as $day => $allWorkLogsByDay) {
            $containsBusinessDay = false;
            $containsHomeDay = false;
            $containsSickDay = false;
            $containsTimeOffDay = false;
            $containsVacationDay = false;

            $standardWorkLogs = [];

            $workTime = 0;
            $workTimeWithoutCorrection = 0;
            $breakTime = 0;

            // Split work logs into groups by its type and calculate work time of standard work logs.
            foreach ($allWorkLogsByDay as $standardWorkLog) {
                if ($standardWorkLog instanceof WorkLog) {
                    $standardWorkLogs[] = $standardWorkLog;

                    // Get work time of current work log
                    $currentWorkTimeDiff = $standardWorkLog->getEndTime()->diff($standardWorkLog->getStartTime());
                    $currentWorkTime = $currentWorkTimeDiff->h + ($currentWorkTimeDiff->i / 60);

                    // Add work time of current work log to total work time.
                    $workTimeWithoutCorrection += $currentWorkTime;

                    // If current work log is longer that 6 hours, 15 minutes break is added.
                    if ($currentWorkTime > 6) {
                        $workTime += $currentWorkTime - 0.25;
                        $breakTime += 0.25;
                    } else {
                        $workTime += $currentWorkTime;
                    }
                } elseif ($standardWorkLog instanceof BusinessTripWorkLog && $standardWorkLog->getTimeApproved()) {
                    $containsBusinessDay = true;
                } elseif ($standardWorkLog instanceof HomeOfficeWorkLog && $standardWorkLog->getTimeApproved()) {
                    $containsHomeDay = true;
                } elseif ($standardWorkLog instanceof SickDayWorkLog) {
                    $containsSickDay = true;
                } elseif ($standardWorkLog instanceof TimeOffWorkLog && $standardWorkLog->getTimeApproved()) {
                    $containsTimeOffDay = true;
                } elseif ($standardWorkLog instanceof VacationWorkLog && $standardWorkLog->getTimeApproved()) {
                    $containsVacationDay = true;
                }
            }

            // Calculate break time between standard work logs if there is more than one
            if (count($standardWorkLogs) > 1) {
                // Sort standard work logs by its start time
                usort($standardWorkLogs, function (WorkLog $a, WorkLog $b) {
                    if ($a->getStartTime() === $b->getStartTime()) {
                        return 0;
                    }

                    return $a->getStartTime() < $b->getStartTime() ? -1 : 1;
                });

                // Split standard work logs to first element and rest of array
                $previousWorkLog = $standardWorkLogs[0];
                $otherWorkLogs = array_slice($standardWorkLogs, 1);

                // Calculate break time between standard work logs
                foreach ($otherWorkLogs as $standardWorkLog) {
                    $currentBreakTimeDiff = $standardWorkLog->getStartTime()->diff($previousWorkLog->getEndTime());
                    $currentBreakTime = $currentBreakTimeDiff->h + ($currentBreakTimeDiff->i / 60);

                    // Take in account only current break time that is equal or longer that 15 minutes
                    if ($currentBreakTime >= 0.25) {
                        $breakTime += $currentBreakTime;
                    }

                    $previousWorkLog = $standardWorkLog;
                }
            }

            // Correct work and break times according to necessary breaks
            if (count($standardWorkLogs) > 0) {
                $config = $this->configService->getConfig();

                $lowerLimit = $config->getWorkedHoursLimits()['lowerLimit'];
                $upperLimit = $config->getWorkedHoursLimits()['upperLimit'];

                if (
                    $workTimeWithoutCorrection > $lowerLimit['limit'] / 3600
                    && $workTimeWithoutCorrection <= $upperLimit['limit'] / 3600
                    && $breakTime < abs($lowerLimit['changeBy'] / 3600)
                ) {
                    $timeToDeduct = abs($lowerLimit['changeBy'] / 3600) - $breakTime;
                    $workTime -= $timeToDeduct;
                } elseif (
                    $workTimeWithoutCorrection > $upperLimit['limit'] / 3600
                    && $breakTime < abs($upperLimit['changeBy'] / 3600)
                ) {
                    $timeToDeduct = abs($upperLimit['changeBy'] / 3600) - $breakTime;
                    $workTime -= $timeToDeduct;
                }
            }

            if (
                (
                    count($standardWorkLogs) === 0
                    && ($containsBusinessDay || $containsHomeDay || $containsSickDay || $containsTimeOffDay)
                ) || $containsVacationDay
            ) {
                $workTime = $workHours->getRequiredHours();
            } elseif ($containsSickDay && count($standardWorkLogs) > 0) {
                $workTime = min($workTimeWithoutCorrection, $workHours->getRequiredHours());
            }

            $allWorkLogWorkTime[$day] = $workTime;
        }

        return array_sum($allWorkLogWorkTime);
    }

    /**
     * @throws \Exception
     */
    public function calculateRequiredHours(WorkMonth $workMonth): float
    {
        $config = $this->configService->getConfig();
        $workingDaysInMonth = 0;

        $isWeekend = function ($date) {
            return $date->format('l') === 'Saturday' || $date->format('l') === 'Sunday';
        };

        $isHoliday = function ($date) use ($config) {
            foreach ($config->getSupportedHolidays() as $supportedHoliday) {
                if ($supportedHoliday->getDate()->format('Y-m-d') === $date->format('Y-m-d')) {
                    return true;
                }
            }

            return false;
        };

        for (
            $date = (new \DateTime())->setDate($workMonth->getYear()->getYear(), $workMonth->getMonth(), 1);
            (int) $date->format('m') === $workMonth->getMonth();
            $date->add(new \DateInterval('P1D'))
        ) {
            if (!$isWeekend($date) && !$isHoliday($date)) {
                ++$workingDaysInMonth;
            }
        }

        $workHours = $this->workHoursRepository->findOne(
            $workMonth->getYear(),
            $workMonth->getMonth(),
            $workMonth->getUser()
        );

        if (!$workHours) {
            throw new \Exception('Work hours has not been found.');
        }

        return $workingDaysInMonth * $workHours->getRequiredHours();
    }
}
