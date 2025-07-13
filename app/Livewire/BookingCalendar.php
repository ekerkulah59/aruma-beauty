<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\Service;
use App\Services\CalendarServiceContract;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class BookingCalendar extends Component
{

    public $events = [];
    public $selectedDate;
    public $selectedTime;
    public $selectedService;
    public $showBookingModal = false;
    public $availableTimeSlots = [];
    public $services;

    public function mount()
    {
        $this->services = Service::all();
    }

    public function getEvents()
    {
        $bookings = Booking::with('service')
            ->where('status', '!=', 'cancelled')
            ->get()
            ->map(function ($booking) {
                if (!$booking->service) {
                    return null;
                }

                // Ensure we're working in the salon's timezone
                $start = Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->booking_time, 'America/New_York');
                $end = $start->copy()->addMinutes($booking->service->duration);

                return [
                    'id' => $booking->id,
                    'title' => $booking->service->name . ' - ' . $booking->name,
                    'start' => $start->toIso8601String(),
                    'end' => $end->toIso8601String(),
                    'backgroundColor' => $this->getServiceColor($booking->service->category),
                    'borderColor' => $this->getServiceColor($booking->service->category),
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'service' => $booking->service->name,
                        'client' => $booking->name,
                        'phone' => $booking->phone,
                        'email' => $booking->email,
                        'notes' => $booking->notes,
                    ],
                ];
            })->filter()->values();

        return $bookings->toArray();
    }

    #[On('booking-created')]
    public function refreshCalendar()
    {
        $this->dispatch('refreshCalendar');
    }

    #[On('booking-updated')]
    public function handleBookingUpdate()
    {
        $this->dispatch('refreshCalendar');
    }

    public function cancelAppointment($appointmentId)
    {
        try {
            // Ensure user is authenticated
            if (!Auth::check()) {
                session()->flash('error', 'You must be logged in to cancel appointments.');
                return;
            }

            $calendarService = app(CalendarServiceContract::class);
            $success = $calendarService->cancelAppointment($appointmentId, Auth::user());

            if ($success) {
                session()->flash('message', 'Appointment cancelled successfully.');
                $this->dispatch('refreshCalendar');
            } else {
                session()->flash('error', 'Appointment not found or already cancelled.');
            }
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            session()->flash('error', 'You are not authorized to cancel this appointment.');
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while cancelling the appointment.');
        }
    }

    public function getServiceColor($category)
    {
        return match ($category) {
            'Haircuts' => '#4CAF50',
            'Coloring' => '#2196F3',
            'Styling' => '#9C27B0',
            'Treatments' => '#FF9800',
            'Weaves' => '#795548',
            'Braiding' => '#607D8B',
            default => '#009688',
        };
    }

    public function dateClick($date)
    {
        $this->selectedDate = $date;
        $this->loadAvailableTimeSlots($date);
        $this->showBookingModal = true;
    }

    public function loadAvailableTimeSlots($date)
    {
        // Parse date in salon timezone
        $date = Carbon::parse($date, 'America/New_York');
        $startTime = $date->copy()->setTime(9, 0, 0);  // 9:00 AM
        $endTime = $date->copy()->setTime(20, 0, 0);   // 8:00 PM

        // Get all bookings for the selected date
        $bookings = Booking::with('service')
            ->whereDate('booking_date', $date->format('Y-m-d'))
            ->where('status', '!=', 'cancelled')
            ->get();

        // Create array of booked time slots
        $bookedSlots = [];
        foreach ($bookings as $booking) {
            $bookingStart = Carbon::parse($booking->booking_time, 'America/New_York');
            $bookingEnd = $bookingStart->copy()->addMinutes($booking->service->duration);

            // Add all time slots that are booked
            $current = $bookingStart->copy();
            while ($current->lt($bookingEnd)) {
                $bookedSlots[] = $current->format('H:i');
                $current->addMinutes(30);
            }
        }

        // Generate available time slots
        $availableSlots = [];
        $current = $startTime->copy();

        while ($current->lt($endTime)) {
            $timeSlot = $current->format('H:i');

            // Check if this time slot is available
            if (!in_array($timeSlot, $bookedSlots)) {
                $availableSlots[] = $timeSlot;
            }

            $current->addMinutes(30);
        }

        $this->availableTimeSlots = $availableSlots;
    }

    public function isTimeSlotAvailable($date, $time, $serviceId)
    {
        $service = Service::find($serviceId);
        if (!$service) return false;

        // Parse the requested booking time in salon timezone
        $bookingStart = Carbon::parse($date . ' ' . $time, 'America/New_York');
        $bookingEnd = $bookingStart->copy()->addMinutes($service->duration);

        // Check for any overlapping bookings using database-agnostic approach
        $conflictingBookings = Booking::with('service')
            ->whereDate('booking_date', $date)
            ->where('status', '!=', 'cancelled')
            ->get()
            ->filter(function ($booking) use ($bookingStart, $bookingEnd) {
                if (!$booking->service) return false;

                $existingStart = Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->booking_time, 'America/New_York');
                $existingEnd = $existingStart->copy()->addMinutes($booking->service->duration);

                // Check if the time ranges overlap
                return $bookingStart->lt($existingEnd) && $bookingEnd->gt($existingStart);
            });

        return $conflictingBookings->isEmpty();
    }

    public function eventClick($event)
    {
        $this->dispatch('show-booking-details', $event);
    }

    public function render()
    {
        return view('livewire.booking-calendar');
    }
}
