<?php

namespace App\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use App\Entity\BusinessTripWorkLog;
use App\Entity\HomeOfficeWorkLog;
use App\Entity\OvertimeWorkLog;
use App\Entity\TimeOffWorkLog;
use App\Entity\User;
use App\Entity\VacationWorkLog;
use App\Entity\WorkLogInterface;
use App\Entity\WorkMonth;
use App\Event\BusinessTripWorkLogCanceledEvent;
use App\Event\HomeOfficeWorkLogCanceledEvent;
use App\Event\OvertimeWorkLogCanceledEvent;
use App\Event\TimeOffWorkLogCanceledEvent;
use App\Event\VacationWorkLogCanceledEvent;
use App\Repository\WorkMonthRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class WorkLogSubscriber implements EventSubscriberInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var WorkMonthRepository
     */
    private $workMonthRepository;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        TokenStorageInterface $tokenStorage,
        WorkMonthRepository $workMonthRepository
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->tokenStorage = $tokenStorage;
        $this->workMonthRepository = $workMonthRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['addWorkMonth', EventPriorities::PRE_VALIDATE],
                ['deleteWorkLog', EventPriorities::PRE_WRITE],
                ['checkWorkMonthStatus', EventPriorities::PRE_WRITE],
                ['resetWorkMonthStatus', EventPriorities::PRE_WRITE],
            ],
        ];
    }

    /**
     * @throws InvalidArgumentException
     */
    public function addWorkMonth(GetResponseForControllerResultEvent $event): void
    {
        $workLog = $event->getControllerResult();
        if (!$workLog instanceof WorkLogInterface) {
            return;
        }

        $method = $event->getRequest()->getMethod();

        if (Request::METHOD_POST !== $method) {
            return;
        }

        $token = $this->tokenStorage->getToken();

        if (!$token || !$token->getUser() instanceof User) {
            throw new InvalidArgumentException('Cannot create work log without user.');
        }

        $workMonth = $this->workMonthRepository->findByWorkLogAndUser($workLog, $token->getUser());

        if (!$workMonth) {
            throw new InvalidArgumentException('Cannot create work log without work month.');
        }

        $workLog->setWorkMonth($workMonth);
    }

    public function deleteWorkLog(GetResponseForControllerResultEvent $event): void
    {
        $workLog = $event->getControllerResult();
        if (!$workLog instanceof WorkLogInterface) {
            return;
        }

        $method = $event->getRequest()->getMethod();

        if (Request::METHOD_DELETE !== $method) {
            return;
        }

        $supervisor = $workLog->getWorkMonth()->getUser()->getSupervisor();

        if ($workLog instanceof BusinessTripWorkLog && $workLog->getTimeApproved() !== null) {
            $this->eventDispatcher->dispatch(
                new BusinessTripWorkLogCanceledEvent($workLog, $supervisor),
                BusinessTripWorkLogCanceledEvent::CANCELED
            );
        } elseif ($workLog instanceof HomeOfficeWorkLog && $workLog->getTimeApproved() !== null) {
            $this->eventDispatcher->dispatch(
                new HomeOfficeWorkLogCanceledEvent($workLog, $supervisor),
                HomeOfficeWorkLogCanceledEvent::CANCELED
            );
        } elseif ($workLog instanceof OvertimeWorkLog && $workLog->getTimeApproved() !== null) {
            $this->eventDispatcher->dispatch(
                new OvertimeWorkLogCanceledEvent($workLog, $supervisor),
                OvertimeWorkLogCanceledEvent::CANCELED
            );
        } elseif ($workLog instanceof TimeOffWorkLog && $workLog->getTimeApproved() !== null) {
            $this->eventDispatcher->dispatch(
                new TimeOffWorkLogCanceledEvent($workLog, $supervisor),
                TimeOffWorkLogCanceledEvent::CANCELED
            );
        } elseif ($workLog instanceof VacationWorkLog && $workLog->getTimeApproved() !== null) {
            $this->eventDispatcher->dispatch(
                new VacationWorkLogCanceledEvent($workLog, $supervisor),
                VacationWorkLogCanceledEvent::CANCELED
            );
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public function checkWorkMonthStatus(GetResponseForControllerResultEvent $event): void
    {
        $workLog = $event->getControllerResult();
        if (!$workLog instanceof WorkLogInterface) {
            return;
        }

        $method = $event->getRequest()->getMethod();

        if (Request::METHOD_POST !== $method && Request::METHOD_DELETE !== $method) {
            return;
        }

        if ($workLog->getWorkMonth()->getStatus() === WorkMonth::STATUS_APPROVED) {
            throw new InvalidArgumentException('Cannot add or delete work log to closed work month.');
        }
    }

    public function resetWorkMonthStatus(GetResponseForControllerResultEvent $event): void
    {
        $workLog = $event->getControllerResult();
        if (!$workLog instanceof WorkLogInterface) {
            return;
        }

        $method = $event->getRequest()->getMethod();

        if (Request::METHOD_POST !== $method && Request::METHOD_PUT !== $method) {
            return;
        }

        if ($workLog->getWorkMonth()->getStatus() === WorkMonth::STATUS_WAITING_FOR_APPROVAL) {
            $workLog->getWorkMonth()->setStatus(WorkMonth::STATUS_OPENED);
        }
    }
}
