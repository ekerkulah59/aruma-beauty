<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;

class BookingConfirmation extends Component
{
    public $booking;
    public $showRescheduleModal = false;
    public $newDate;
    public $newTime;
    public $availableTimeSlots = [];

    public function mount(Booking $booking)
    {
        $this->booking = $booking;
        $this->newDate = $booking->booking_date->format('Y-m-d');
        $this->loadAvailableTimeSlots();
    }

    public function loadAvailableTimeSlots()
    {
        $date = \Carbon\Carbon::parse($this->newDate);
        $bookedSlots = Booking::whereDate('booking_date', $date)
            ->where('id', '!=', $this->booking->id)
            ->pluck('booking_time')
            ->toArray();

        $allSlots = $this->generateTimeSlots();
        $this->availableTimeSlots = array_diff($allSlots, $bookedSlots);
    }

    private function generateTimeSlots()
    {
        $slots = [];
        $start = \Carbon\Carbon::parse('09:00');
        $end = \Carbon\Carbon::parse('17:00');

        while ($start->copy()->addMinutes(30)->lte($end)) {
            $slots[] = $start->format('H:i');
            $start->addMinutes(30);
        }

        return $slots;
    }

    public function updatedNewDate()
    {
        $this->loadAvailableTimeSlots();
    }

    public function reschedule()
    {
        $this->validate([
            'newDate' => 'required|date|after:today',
            'newTime' => 'required',
        ]);

        $this->booking->update([
            'booking_date' => $this->newDate,
            'booking_time' => $this->newTime,
        ]);

        // Send rescheduling confirmation email
        Mail::to($this->booking->email)->send(new \App\Mail\BookingRescheduled($this->booking));

        $this->showRescheduleModal = false;
        session()->flash('message', 'Your appointment has been rescheduled successfully.');
    }

    public function cancel()
    {
        $this->booking->update(['status' => 'cancelled']);

        // Send cancellation email
        Mail::to($this->booking->email)->send(new \App\Mail\BookingCancelled($this->booking));

        session()->flash('message', 'Your appointment has been cancelled.');
        return redirect()->route('book');
    }

    public function printConfirmation()
    {
        return view('livewire.booking-confirmation-print', ['booking' => $this->booking]);
    }

    public function render()
    {
        return view('livewire.booking-confirmation');
    }
}