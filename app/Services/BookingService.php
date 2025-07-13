<?php

namespace App\Services;

use App\Events\BookingCreated;
use App\Models\Booking;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BookingService
{
    private CalendarServiceContract $calendarService;

    public function __construct(CalendarServiceContract $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    /**
     * Update booking details with overlap validation
     */
    public function updateBooking(Booking $booking, array $data): Booking
    {
        DB::beginTransaction();

        try {
            $originalBookingId = $booking->id;
            $originalDate = $booking->booking_date;
            $originalTime = $booking->booking_time;
            $originalServiceId = $booking->service_id;

            // Validate service if provided
            if (isset($data['service_id'])) {
                $service = Service::find($data['service_id']);
                if (!$service) {
                    throw ValidationException::withMessages(['service_id' => 'Invalid service selected.']);
                }
            }

            // Check for time/date changes that require overlap validation
            $dateTimeChanged = isset($data['booking_date']) || isset($data['booking_time']) || isset($data['service_id']);

            if ($dateTimeChanged) {
                $newDate = isset($data['booking_date']) ? Carbon::parse($data['booking_date'], 'America/New_York') : $booking->booking_date;
                $newTime = $data['booking_time'] ?? $booking->booking_time;
                $newServiceId = $data['service_id'] ?? $booking->service_id;
                $newService = Service::find($newServiceId);

                // Create the new appointment datetime
                $newAppointmentDateTime = Carbon::parse($newDate->format('Y-m-d') . ' ' . $newTime, 'America/New_York');

                // Validate business hours
                $this->validateBusinessHours($newAppointmentDateTime, $newService);

                // Check for overlaps (excluding the current booking)
                $this->validateNoOverlaps($newAppointmentDateTime, $newService, $originalBookingId);
            }

            // Update the booking
            $booking->update(array_filter([
                'service_id' => $data['service_id'] ?? null,
                'booking_date' => isset($data['booking_date']) ? Carbon::parse($data['booking_date'], 'America/New_York')->format('Y-m-d') : null,
                'booking_time' => $data['booking_time'] ?? null,
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'] ?? null,
            ], function ($value) {
                return $value !== null;
            }));

            // Log the update
            Log::info('Booking updated', [
                'booking_id' => $booking->id,
                'updated_fields' => array_keys($data),
                'admin_action' => true
            ]);

            // Dispatch event if date/time changed
            if ($dateTimeChanged) {
                event(new BookingCreated($booking));
            }

            DB::commit();
            return $booking->fresh();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update booking', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Cancel a booking
     */
    public function cancelBooking(Booking $booking, string $reason = null): Booking
    {
        DB::beginTransaction();

        try {
            $booking->update([
                'status' => 'cancelled',
                'notes' => $booking->notes . ($reason ? "\n\nCancellation reason: " . $reason : "\n\nCancelled by admin")
            ]);

            Log::info('Booking cancelled', [
                'booking_id' => $booking->id,
                'reason' => $reason,
                'admin_action' => true
            ]);

            // Dispatch calendar refresh event
            event(new BookingCreated($booking));

            DB::commit();
            return $booking->fresh();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to cancel booking', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update booking status
     */
    public function updateBookingStatus(Booking $booking, string $status): Booking
    {
        $validStatuses = ['pending', 'confirmed', 'completed', 'cancelled', 'rescheduled', 'no_show'];

        if (!in_array($status, $validStatuses)) {
            throw ValidationException::withMessages(['status' => 'Invalid status provided.']);
        }

        DB::beginTransaction();

        try {
            $oldStatus = $booking->status;
            $booking->update(['status' => $status]);

            Log::info('Booking status updated', [
                'booking_id' => $booking->id,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'admin_action' => true
            ]);

            // Dispatch calendar refresh event
            event(new BookingCreated($booking));

            DB::commit();
            return $booking->fresh();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update booking status', [
                'booking_id' => $booking->id,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get available time slots for a specific date and service (excluding a specific booking)
     */
    public function getAvailableTimeSlotsForEdit(Carbon $date, int $serviceId, int $excludeBookingId = null): array
    {
        $slots = $this->calendarService->getAvailableSlots($date, $serviceId);

        // If we're editing an existing booking, we need to also include its current time slot
        if ($excludeBookingId) {
            $existingBooking = Booking::find($excludeBookingId);
            if ($existingBooking && $existingBooking->booking_date->format('Y-m-d') === $date->format('Y-m-d')) {
                $existingTime = Carbon::parse($existingBooking->booking_date->format('Y-m-d') . ' ' . $existingBooking->booking_time, 'America/New_York');

                // Add the existing time slot to available slots if it's not already there
                $existingTimeFound = false;
                foreach ($slots as $slot) {
                    if ($slot->format('H:i') === $existingTime->format('H:i')) {
                        $existingTimeFound = true;
                        break;
                    }
                }

                if (!$existingTimeFound) {
                    $slots[] = $existingTime;
                    // Sort the slots
                    usort($slots, function ($a, $b) {
                        return $a->timestamp <=> $b->timestamp;
                    });
                }
            }
        }

        return $slots;
    }

    /**
     * Validate business hours for appointment
     */
    private function validateBusinessHours(Carbon $appointmentDateTime, Service $service): void
    {
        $dayOfWeek = $appointmentDateTime->dayOfWeek;

        // Check if salon is closed on this day (Sunday = 0)
        if ($dayOfWeek === 0) {
            throw ValidationException::withMessages(['booking_date' => 'Sorry, we are closed on Sundays.']);
        }

        $businessHours = [
            1 => ['09:00', '20:00'], // Monday
            2 => ['09:00', '20:00'], // Tuesday
            3 => ['09:00', '20:00'], // Wednesday
            4 => ['09:00', '20:00'], // Thursday
            5 => ['09:00', '20:00'], // Friday
            6 => ['09:00', '18:00'], // Saturday
        ];

        if (!isset($businessHours[$dayOfWeek])) {
            throw ValidationException::withMessages(['booking_date' => 'Sorry, we are closed on this day.']);
        }

        [$openTime, $closeTime] = $businessHours[$dayOfWeek];
        $appointmentTime = $appointmentDateTime->format('H:i');
        $appointmentEndTime = $appointmentDateTime->copy()->addMinutes($service->duration)->format('H:i');

        if ($appointmentTime < $openTime || $appointmentEndTime > $closeTime) {
            throw ValidationException::withMessages([
                'booking_time' => "Appointment would be outside business hours ({$openTime} - {$closeTime})."
            ]);
        }
    }

    /**
     * Validate no overlapping appointments
     */
    private function validateNoOverlaps(Carbon $appointmentDateTime, Service $service, int $excludeBookingId = null): void
    {
        $appointmentEnd = $appointmentDateTime->copy()->addMinutes($service->duration);

        $query = Booking::with('service')
            ->whereDate('booking_date', $appointmentDateTime->format('Y-m-d'))
            ->where('status', '!=', 'cancelled');

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        $conflictingBookings = $query->get()->filter(function ($booking) use ($appointmentDateTime, $appointmentEnd) {
            if (!$booking->service) return false;

            $existingStart = Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->booking_time, 'America/New_York');
            $existingEnd = $existingStart->copy()->addMinutes($booking->service->duration);

            // Check if appointments overlap
            return $appointmentDateTime->lt($existingEnd) && $appointmentEnd->gt($existingStart);
        });

        if ($conflictingBookings->isNotEmpty()) {
            $conflictDetails = $conflictingBookings->map(function ($booking) {
                return $booking->booking_time . ' (' . $booking->service->name . ')';
            })->join(', ');

            throw ValidationException::withMessages([
                'booking_time' => "This time conflicts with existing appointments: {$conflictDetails}"
            ]);
        }
    }

    /**
     * Get booking statistics for admin dashboard
     */
    public function getBookingStats(): array
    {
        $today = Carbon::today('America/New_York');

        return [
            'total_bookings' => Booking::count(),
            'today_bookings' => Booking::whereDate('booking_date', $today)->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'cancelled_bookings' => Booking::where('status', 'cancelled')->count(),
            'completed_bookings' => Booking::where('status', 'completed')->count(),
        ];
    }
}
