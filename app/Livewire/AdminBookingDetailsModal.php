<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\Service;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class AdminBookingDetailsModal extends Component
{
    public $showModal = false;
    public $booking = null;
    public $editMode = false;
    public $availableServices = [];
    public $availableTimeSlots = [];
    public $statusOptions = [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'rescheduled' => 'Rescheduled',
        'no_show' => 'No Show'
    ];

    // Form fields
    public $name = '';
    public $email = '';
    public $phone = '';
    public $notes = '';
    public $service_id = '';
    public $booking_date = '';
    public $booking_time = '';
    public $status = '';
    public $cancellation_reason = '';

    protected $listeners = ['show-booking-details' => 'showBookingDetails'];

    public function mount()
    {
        $this->availableServices = Service::all();
    }

    #[On('show-booking-details')]
    public function showBookingDetails($eventData)
    {
        // Check authorization first
        if (!Auth::check() || !Auth::user()->is_admin) {
            $this->dispatch('unauthorized-access');
            return;
        }

        $bookingId = $eventData['id'] ?? null;
        if (!$bookingId) {
            return;
        }

        $this->booking = Booking::with('service')->find($bookingId);
        if (!$this->booking) {
            $this->dispatch('booking-not-found');
            return;
        }

        $this->loadBookingData();
        $this->showModal = true;
    }

    private function loadBookingData()
    {
        $this->name = $this->booking->name;
        $this->email = $this->booking->email;
        $this->phone = $this->booking->phone;
        $this->notes = $this->booking->notes ?? '';
        $this->service_id = $this->booking->service_id;
        $this->booking_date = $this->booking->booking_date->format('Y-m-d');
        $this->booking_time = $this->booking->booking_time;
        $this->status = $this->booking->status;
        $this->cancellation_reason = '';

        // Load available time slots for current date/service
        $this->loadAvailableTimeSlots();
    }

    public function updatedBookingDate()
    {
        $this->loadAvailableTimeSlots();
    }

    public function updatedServiceId()
    {
        $this->loadAvailableTimeSlots();
    }

    private function loadAvailableTimeSlots()
    {
        if ($this->booking_date && $this->service_id) {
            try {
                $date = Carbon::parse($this->booking_date, 'America/New_York');
                $bookingService = app(BookingService::class);
                $this->availableTimeSlots = $bookingService->getAvailableTimeSlotsForEdit(
                    $date,
                    $this->service_id,
                    $this->booking->id
                );
            } catch (\Exception $e) {
                $this->availableTimeSlots = [];
            }
        }
    }

    public function enableEditMode()
    {
        if (!Auth::user()->is_admin) {
            $this->addError('authorization', 'You are not authorized to edit bookings.');
            return;
        }

        $this->editMode = true;
        $this->loadAvailableTimeSlots();
    }

    public function cancelEdit()
    {
        $this->editMode = false;
        $this->loadBookingData(); // Reset form data
        $this->resetValidation();
    }

    public function updateBooking()
    {
        if (!Auth::user()->is_admin) {
            $this->addError('authorization', 'You are not authorized to update bookings.');
            return;
        }

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'service_id' => 'required|exists:services,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|string',
            'status' => 'required|in:' . implode(',', array_keys($this->statusOptions)),
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $bookingService = app(BookingService::class);

            $updatedBooking = $bookingService->updateBooking($this->booking, [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'service_id' => $this->service_id,
                'booking_date' => $this->booking_date,
                'booking_time' => $this->booking_time,
                'status' => $this->status,
                'notes' => $this->notes,
            ]);

            $this->booking = $updatedBooking;
            $this->editMode = false;

            session()->flash('message', 'Booking updated successfully.');
            $this->dispatch('booking-updated');

        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        } catch (\Exception $e) {
            $this->addError('general', 'An error occurred while updating the booking: ' . $e->getMessage());
        }
    }

    public function updateStatus($newStatus)
    {
        if (!Auth::user()->is_admin) {
            $this->addError('authorization', 'You are not authorized to update booking status.');
            return;
        }

        try {
            $bookingService = app(BookingService::class);
            $this->booking = $bookingService->updateBookingStatus($this->booking, $newStatus);
            $this->status = $newStatus;

            session()->flash('message', 'Booking status updated successfully.');
            $this->dispatch('booking-updated');

        } catch (ValidationException $e) {
            $this->addError('status', $e->getMessage());
        } catch (\Exception $e) {
            $this->addError('general', 'An error occurred while updating the status: ' . $e->getMessage());
        }
    }

    public function cancelBooking()
    {
        if (!Auth::user()->is_admin) {
            $this->addError('authorization', 'You are not authorized to cancel bookings.');
            return;
        }

        try {
            $bookingService = app(BookingService::class);
            $this->booking = $bookingService->cancelBooking($this->booking, $this->cancellation_reason);
            $this->status = 'cancelled';

            session()->flash('message', 'Booking cancelled successfully.');
            $this->dispatch('booking-updated');
            $this->closeModal();

        } catch (\Exception $e) {
            $this->addError('general', 'An error occurred while cancelling the booking: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editMode = false;
        $this->booking = null;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->notes = '';
        $this->service_id = '';
        $this->booking_date = '';
        $this->booking_time = '';
        $this->status = '';
        $this->cancellation_reason = '';
        $this->availableTimeSlots = [];
    }

    public function getBookingDateTimeAttribute()
    {
        if (!$this->booking) return null;

        return Carbon::parse($this->booking->booking_date->format('Y-m-d') . ' ' . $this->booking->booking_time, 'America/New_York');
    }

    public function getBookingEndTimeAttribute()
    {
        if (!$this->booking || !$this->booking->service) return null;

        return $this->getBookingDateTimeAttribute()->copy()->addMinutes($this->booking->service->duration);
    }

    public function getIsCompletedProperty()
    {
        return $this->booking && strtolower($this->booking->status) === 'completed';
    }

    public function render()
    {
        return view('livewire.admin-booking-details-modal');
    }
}
