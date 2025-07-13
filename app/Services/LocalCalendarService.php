<?php

namespace App\Services;

use App\Events\BookingCreated;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LocalCalendarService implements CalendarServiceContract
{
    private array $businessHours = [
        1 => ['09:00', '20:00'], // Monday
        2 => ['09:00', '20:00'], // Tuesday
        3 => ['09:00', '20:00'], // Wednesday
        4 => ['09:00', '20:00'], // Thursday
        5 => ['09:00', '20:00'], // Friday
        6 => ['09:00', '18:00'], // Saturday
        // Sunday (0) is not included - salon is closed
    ];

    /**
     * Convert user input time to proper Carbon instance in salon timezone
     */
    public function parseUserTime(string $userInput): Carbon
    {
        return Carbon::parse($userInput, 'America/New_York');
    }

    /**
     * Get current time in salon timezone
     */
    public function now(): Carbon
    {
        return Carbon::now('America/New_York');
    }

    /**
     * Get today's date in salon timezone
     */
    public function today(): Carbon
    {
        return Carbon::today('America/New_York');
    }

    public function getAvailableDates(int $serviceId, int $numberOfDays = 7): array
    {
        $service = Service::find($serviceId);
        if (!$service) {
            return [];
        }

        $availableDates = [];
        $startDate = $this->today();
        $daysChecked = 0;
        $maxDaysToCheck = $numberOfDays * 2; // Check more days to account for closed days

        while (count($availableDates) < $numberOfDays && $daysChecked < $maxDaysToCheck) {
            $date = $startDate->copy()->addDays($daysChecked);
            $dayOfWeek = $date->dayOfWeek;

            // Skip Sundays (day 0) as salon is closed
            if ($dayOfWeek === 0) {
                $daysChecked++;
                continue;
            }

            // Check if salon is open on this day and has availability
            if (isset($this->businessHours[$dayOfWeek]) && $this->hasAvailability($date, $serviceId)) {
                $availableDates[] = $date;
            }

            $daysChecked++;
        }

        return $availableDates;
    }

    public function getAvailableSlots(Carbon $date, int $serviceId): array
    {
        $service = Service::find($serviceId);
        if (!$service) {
            return [];
        }
        $duration = $service->duration;

        $dayOfWeek = $date->dayOfWeek;
        if (!isset($this->businessHours[$dayOfWeek])) {
            return [];
        }

        [$openTime, $closeTime] = $this->businessHours[$dayOfWeek];
        $openDateTime = $date->copy()->setTimeFromTimeString($openTime);
        $closeDateTime = $date->copy()->setTimeFromTimeString($closeTime);

        // Get all bookings for the day and create "busy blocks"
        $bookingsOnDay = Booking::with('service')
            ->whereDate('booking_date', $date->format('Y-m-d'))
            ->where('status', '!=', 'cancelled')
            ->orderBy('booking_time')
            ->get();

        $busyBlocks = [];
        foreach ($bookingsOnDay as $booking) {
            if (!$booking->service) continue;

            // Create a proper start time by copying the date and setting the time
            $startTime = $booking->booking_date->copy()->setTimeFromTimeString($booking->booking_time);
            $endTime = $startTime->copy()->addMinutes($booking->service->duration);

            $busyBlocks[] = [
                'start' => $startTime,
                'end' => $endTime
            ];
        }

        // Merge overlapping busy blocks (shouldn't happen with proper booking, but safety first)
        $busyBlocks = $this->mergeBusyBlocks($busyBlocks);

        // Find available slots in the gaps between busy blocks
        $availableSlots = [];
        $currentTime = $openDateTime->copy();

        foreach ($busyBlocks as $busyBlock) {
            // Check for available slots before this busy block
            while ($currentTime->copy()->addMinutes($duration) <= $busyBlock['start']) {
                $availableSlots[] = $currentTime->copy();
                $currentTime->addMinutes(30); // 30-minute intervals
            }

            // Move current time to after this busy block
            $currentTime = $busyBlock['end']->copy();

            // Align to next 30-minute boundary
            $minutes = $currentTime->minute;
            $remainder = $minutes % 30;
            if ($remainder !== 0) {
                $currentTime->addMinutes(30 - $remainder);
            }
        }

        // Check for available slots after the last busy block
        while ($currentTime->copy()->addMinutes($duration) <= $closeDateTime) {
            $availableSlots[] = $currentTime->copy();
            $currentTime->addMinutes(30);
        }

        return $availableSlots;
    }

    /**
     * Merge overlapping busy blocks to optimize slot calculation
     */
    private function mergeBusyBlocks(array $busyBlocks): array
    {
        if (empty($busyBlocks)) {
            return [];
        }

        // Sort by start time
        usort($busyBlocks, function ($a, $b) {
            return $a['start']->timestamp <=> $b['start']->timestamp;
        });

        $merged = [$busyBlocks[0]];

        for ($i = 1; $i < count($busyBlocks); $i++) {
            $current = $busyBlocks[$i];
            $lastMerged = &$merged[count($merged) - 1];

            // If current block overlaps with the last merged block, merge them
            if ($current['start'] <= $lastMerged['end']) {
                $lastMerged['end'] = max($lastMerged['end'], $current['end']);
            } else {
                // No overlap, add as new block
                $merged[] = $current;
            }
        }

        return $merged;
    }

    public function bookAppointment(array $clientInfo, int $serviceId, Carbon $appointmentDate): Booking
    {
        $service = Service::find($serviceId);
        if (!$service) {
            throw new \Exception('Invalid service selected.');
        }

        // Check if appointment is on a valid business day
        $dayOfWeek = $appointmentDate->dayOfWeek;
        if ($dayOfWeek === 0) { // Sunday
            throw new \Exception('Sorry, we are closed on Sundays. Please select another day.');
        }

        if (!isset($this->businessHours[$dayOfWeek])) {
            throw new \Exception('Sorry, we are closed on this day. Please select another day.');
        }

        // Check if appointment is within business hours
        [$openTime, $closeTime] = $this->businessHours[$dayOfWeek];
        $appointmentTime = $appointmentDate->format('H:i');
        $appointmentEndTime = $appointmentDate->copy()->addMinutes($service->duration)->format('H:i');

        if ($appointmentTime < $openTime || $appointmentEndTime > $closeTime) {
            throw new \Exception("Sorry, this appointment would be outside our business hours ({$openTime} - {$closeTime}). Please select a different time.");
        }

        if (!$this->isSlotAvailable($appointmentDate, $service->duration)) {
            throw new \Exception('This time slot is no longer available. Please select another time.');
        }

        $booking = Booking::create([
            'service_id' => $serviceId,
            'booking_date' => $appointmentDate->format('Y-m-d'),
            'booking_time' => $appointmentDate->format('H:i:s'),
            'name' => $clientInfo['name'],
            'email' => $clientInfo['email'],
            'phone' => $clientInfo['phone'],
            'notes' => 'Booked via AI Chatbot',
            'status' => 'confirmed',
        ]);

        Log::info('Appointment booked', ['booking_id' => $booking->id]);

        BookingCreated::dispatch($booking);

        return $booking;
    }

    public function cancelAppointment(int $appointmentId, ?User $user = null): bool
    {
        $booking = Booking::find($appointmentId);
        if (!$booking) {
            return false;
        }

        // Authorization check - only allow if user is authenticated and is an admin
        // In a real-world scenario, you might also allow the booking owner to cancel
        if ($user && !$user->hasRole('admin')) {
            throw new \Illuminate\Auth\Access\AuthorizationException('You are not authorized to cancel this appointment.');
        }

        $booking->update(['status' => 'cancelled']);
        Log::info('Appointment cancelled', [
            'booking_id' => $appointmentId,
            'cancelled_by' => $user ? $user->id : 'system'
        ]);
        return true;
    }

    private function hasAvailability(Carbon $date, int $serviceId): bool
    {
        return !empty($this->getAvailableSlots($date, $serviceId));
    }

    private function isSlotAvailable(Carbon $appointmentDate, int $durationMinutes, $bookingsOnDay = null): bool
    {
        $newStartTime = $appointmentDate;
        $newEndTime = $appointmentDate->copy()->addMinutes($durationMinutes);

        // Fetch bookings for the day if not provided
        if ($bookingsOnDay === null) {
            $bookingsOnDay = Booking::with('service')
                ->whereDate('booking_date', $appointmentDate->format('Y-m-d'))
                ->where('status', '!=', 'cancelled')
                ->get();
        }

        foreach ($bookingsOnDay as $existingBooking) {
            if (!$existingBooking->service) continue;

            $existingStartTime = $existingBooking->booking_date->copy()->setTimeFromTimeString($existingBooking->booking_time);
            $existingEndTime = $existingStartTime->copy()->addMinutes($existingBooking->service->duration);

            // The core logic for detecting an overlap.
            // An overlap occurs if the new appointment's start time is before the existing one ends,
            // AND the new appointment's end time is after the existing one starts.
            if ($newStartTime < $existingEndTime && $newEndTime > $existingStartTime) {
                return false;
            }
        }

        return true;
    }
}
