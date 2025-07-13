<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Booking;
use App\Models\User;

interface CalendarServiceContract
{
    /**
     * Get available dates for a given service type.
     *
     * @param int $serviceId
     * @param int $numberOfDays
     * @return array
     */
    public function getAvailableDates(int $serviceId, int $numberOfDays = 7): array;

    /**
     * Get available time slots for a given date and service type.
     *
     * @param Carbon $date
     * @param int $serviceId
     * @return array
     */
    public function getAvailableSlots(Carbon $date, int $serviceId): array;

    /**
     * Book an appointment.
     *
     * @param array $clientInfo
     * @param int $serviceId
     * @param Carbon $appointmentDate
     * @return Booking
     */
    public function bookAppointment(array $clientInfo, int $serviceId, Carbon $appointmentDate): Booking;

    /**
     * Cancel an existing appointment
     */
    public function cancelAppointment(int $appointmentId, ?User $user = null): bool;
}
