<?php

namespace Igniter\Reservation\Listeners;

use Admin\Models\Reservations_model;
use Carbon\Carbon;
use Igniter\Local\Facades\Location as LocationFacade;
use Illuminate\Contracts\Events\Dispatcher;

class MaxGuestSizePerTimeslotReached
{
    protected static $reservationsCache = [];

    public function subscribe(Dispatcher $dispatcher)
    {
        $dispatcher->listen('igniter.reservation.isFullyBookedOn', __CLASS__.'@isFullyBookedOn');
    }

    public function isFullyBookedOn($timeslot, $guestNum)
    {
        $locationModel = LocationFacade::current();
        if (!(bool)$locationModel->getOption('limit_reservations'))
            return;

        if (!$limitCount = (int)$locationModel->getOption('limit_guests_count', 20))
            return;

        $totalGuestNumOnThisDay = $this->getGuestNum($timeslot);
        if (!$totalGuestNumOnThisDay)
            return;

        if (($totalGuestNumOnThisDay + $guestNum) > $limitCount)
            return TRUE;
    }

    protected function getGuestNum($timeslot)
    {
        $date = Carbon::parse($timeslot)->toDateString();

        if (array_has(self::$reservationsCache, $date))
            return self::$reservationsCache[$date];

        $startTime = Carbon::parse($timeslot)->subMinutes(2);
        $endTime = Carbon::parse($timeslot)->addMinutes(2);

        $guestNum = Reservations_model::where('location_id', LocationFacade::getId())
            ->where('status_id', '!=', setting('canceled_reservation_status'))
            ->whereBetweenReservationDateTime($startTime->toDateTimeString(), $endTime->toDateTimeString())
            ->sum('guest_num');

        return self::$reservationsCache[$date] = $guestNum;
    }
}
