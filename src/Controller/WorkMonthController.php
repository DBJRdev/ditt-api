<?php

namespace App\Controller;

use App\Entity\BusinessTripWorkLog;
use App\Entity\HomeOfficeWorkLog;
use App\Entity\TimeOffWorkLog;
use App\Entity\User;
use App\Entity\VacationWorkLog;
use App\Entity\WorkMonth;
use App\Event\WorkMonthApprovedEvent;
use App\Repository\BusinessTripWorkLogRepository;
use App\Repository\HomeOfficeWorkLogRepository;
use App\Repository\TimeOffWorkLogRepository;
use App\Repository\UserRepository;
use App\Repository\VacationWorkLogRepository;
use App\Repository\WorkMonthRepository;
use App\Service\WorkMonthService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class WorkMonthController extends Controller
{
    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var WorkMonthRepository
     */
    private $workMonthRepository;

    /**
     * @var WorkMonthService
     */
    private $workMonthService;

    /**
     * @var BusinessTripWorkLogRepository
     */
    private $businessTripWorkLogRepository;

    /**
     * @var HomeOfficeWorkLogRepository
     */
    private $homeOfficeWorkLogRepository;

    /**
     * @var TimeOffWorkLogRepository
     */
    private $timeOffWorkLogRepository;

    /**
     * @var VacationWorkLogRepository
     */
    private $vacationWorkLogRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param NormalizerInterface $normalizer
     * @param UserRepository $userRepository
     * @param WorkMonthRepository $workMonthRepository
     * @param WorkMonthService $workMonthService
     * @param BusinessTripWorkLogRepository $businessTripWorkLogRepository
     * @param HomeOfficeWorkLogRepository $homeOfficeWorkLogRepository
     * @param TimeOffWorkLogRepository $timeOffWorkLogRepository
     * @param VacationWorkLogRepository $vacationWorkLogRepository
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        NormalizerInterface $normalizer,
        UserRepository $userRepository,
        WorkMonthRepository $workMonthRepository,
        WorkMonthService $workMonthService,
        BusinessTripWorkLogRepository $businessTripWorkLogRepository,
        HomeOfficeWorkLogRepository $homeOfficeWorkLogRepository,
        TimeOffWorkLogRepository $timeOffWorkLogRepository,
        VacationWorkLogRepository $vacationWorkLogRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->normalizer = $normalizer;
        $this->userRepository = $userRepository;
        $this->workMonthRepository = $workMonthRepository;
        $this->workMonthService = $workMonthService;
        $this->businessTripWorkLogRepository = $businessTripWorkLogRepository;
        $this->homeOfficeWorkLogRepository = $homeOfficeWorkLogRepository;
        $this->timeOffWorkLogRepository = $timeOffWorkLogRepository;
        $this->vacationWorkLogRepository = $vacationWorkLogRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param int $supervisorId
     * @return Response
     */
    public function specialApprovals(int $supervisorId): Response
    {
        $supervisor = $this->userRepository->getRepository()->find($supervisorId);
        if (!$supervisor || !$supervisor instanceof User) {
            throw $this->createNotFoundException(sprintf('User with id %d was not found', $supervisorId));
        }

        $response = [
            'businessTripWorkLogs' => [],
            'homeOfficeWorkLogs' => [],
            'timeOffWorkLogs' => [],
            'vacationWorkLogs' => [],
        ];

        foreach ($this->businessTripWorkLogRepository->findAllWaitingForApproval($supervisor) as $workLog) {
            $response['businessTripWorkLogs'][] = $this->normalizer->normalize(
                $workLog,
                BusinessTripWorkLog::class,
                ['groups' => ['special_approvals_out_list']]
            );
        }

        foreach ($this->homeOfficeWorkLogRepository->findAllWaitingForApproval($supervisor) as $workLog) {
            $response['homeOfficeWorkLogs'][] = $this->normalizer->normalize(
                $workLog,
                HomeOfficeWorkLog::class,
                ['groups' => ['special_approvals_out_list']]
            );
        }

        foreach ($this->timeOffWorkLogRepository->findAllWaitingForApproval($supervisor) as $workLog) {
            $response['timeOffWorkLogs'][] = $this->normalizer->normalize(
                $workLog,
                TimeOffWorkLog::class,
                ['groups' => ['special_approvals_out_list']]
            );
        }

        foreach ($this->vacationWorkLogRepository->findAllWaitingForApproval($supervisor) as $workLog) {
            $response['vacationWorkLogs'][] = $this->normalizer->normalize(
                $workLog,
                VacationWorkLog::class,
                ['groups' => ['special_approvals_out_list']]
            );
        }

        return JsonResponse::create($response, JsonResponse::HTTP_OK);
    }

    /**
     * @param int $id
     * @return Response
     */
    public function markWaitingForApproval(int $id): Response
    {
        $workMonth = $this->workMonthRepository->getRepository()->find($id);
        if (!$workMonth || !$workMonth instanceof WorkMonth) {
            throw $this->createNotFoundException(sprintf('Work month with id %d was not found.', $id));
        }

        if ($workMonth->getStatus() === WorkMonth::STATUS_APPROVED) {
            return JsonResponse::create(
                ['detail' => 'Work month has been already approved.'], JsonResponse::HTTP_BAD_REQUEST
            );
        }

        if ($workMonth->getStatus() === WorkMonth::STATUS_WAITING_FOR_APPROVAL) {
            return JsonResponse::create(
                ['detail' => 'Work month has been already sent for approval.'], JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $this->workMonthService->markWaitingForApproval($workMonth);

        return JsonResponse::create(
            $this->normalizer->normalize(
                $workMonth,
                WorkMonth::class,
                ['groups' => ['work_month_out_detail']]
            ), JsonResponse::HTTP_OK
        );
    }

    /**
     * @param int $id
     * @return Response
     */
    public function markApproved(int $id): Response
    {
        $workMonth = $this->workMonthRepository->getRepository()->find($id);
        if (!$workMonth || !$workMonth instanceof WorkMonth) {
            throw $this->createNotFoundException(sprintf('Work month with id %d was not found.', $id));
        }

        if ($workMonth->getStatus() === WorkMonth::STATUS_APPROVED) {
            return JsonResponse::create(
                ['detail' => 'Work month has been already approved.'], JsonResponse::HTTP_BAD_REQUEST
            );
        }

        if ($workMonth->getStatus() === WorkMonth::STATUS_OPENED) {
            return JsonResponse::create(
                ['detail' => 'Work month has not been sent for approval yet.'], JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $this->workMonthService->markApproved($workMonth);

        $supervisor = $this->getUser();
        if (!$supervisor) { // This needs to be here for tests to work. In production the condition will never be met.
            $supervisor = new User();
        }
        $this->eventDispatcher->dispatch(
            WorkMonthApprovedEvent::APPROVED,
            new WorkMonthApprovedEvent($workMonth, $supervisor)
        );

        return JsonResponse::create(
            $this->normalizer->normalize(
                $workMonth,
                WorkMonth::class,
                ['groups' => ['work_month_out_detail']]
            ), JsonResponse::HTTP_OK
        );
    }
}
