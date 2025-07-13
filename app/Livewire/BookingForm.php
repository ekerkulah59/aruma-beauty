<?php

namespace App\Livewire;

use App\Models\Service;
use App\Services\CalendarServiceContract;
use App\Exceptions\BookingException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class BookingForm extends Component
{
    public $selectedService;
    public $selectedDate;
    public $selectedTime;
    public $name;
    public $email;
    public $phone;
    public $notes;
    public $availableTimeSlots = [];
    public $services;

    protected $rules = [
        'selectedService' => 'required|exists:services,id',
        'selectedDate' => 'required|date|after_or_equal:today',
        'selectedTime' => 'required',
        'name' => 'required|min:3|max:255|regex:/^[a-zA-Z\s\'-]+$/',
        'email' => 'required|email|max:255',
        'phone' => 'required|max:20|regex:/^[\+]?[1-9][\d]{0,15}$/',
        'notes' => 'nullable|max:1000',
    ];

    protected $messages = [
        'selectedService.required' => 'Please select a service.',
        'selectedService.exists' => 'The selected service is not available.',
        'selectedDate.required' => 'Please select a date for your appointment.',
        'selectedDate.after_or_equal' => 'Cannot book appointments in the past.',
        'selectedTime.required' => 'Please select a time for your appointment.',
        'name.required' => 'Please provide your full name.',
        'name.regex' => 'Name can only contain letters, spaces, hyphens, and apostrophes.',
        'email.required' => 'Please provide your email address.',
        'email.email' => 'Please provide a valid email address.',
        'phone.required' => 'Please provide your phone number.',
        'phone.regex' => 'Please provide a valid phone number.',
        'notes.max' => 'Notes cannot exceed 1000 characters.'
    ];

    public function mount()
    {
        $this->services = Service::active()->get();
        $this->selectedDate = now()->format('Y-m-d');
    }

    public function updatedSelectedDate()
    {
        $this->selectedTime = null;
        $this->loadAvailableTimeSlots();
    }

    public function updatedSelectedService()
    {
        $this->loadAvailableTimeSlots();
    }

    public function loadAvailableTimeSlots()
    {
        if (!$this->selectedDate || !$this->selectedService) {
            $this->availableTimeSlots = [];
            return;
        }

        try {
            $calendarService = app(CalendarServiceContract::class);
            $date = Carbon::parse($this->selectedDate);
            $slots = $calendarService->getAvailableSlots($date, $this->selectedService);

            $this->availableTimeSlots = collect($slots)->map(function ($slot) {
                return $slot->format('H:i');
            })->toArray();
        } catch (\Exception $e) {
            $this->availableTimeSlots = [];
            $this->addError('selectedDate', 'Unable to load available time slots. Please try again.');
        }
    }

    public function submit()
    {
        $this->validate();

        // Additional validation for business hours and salon closure
        $this->validateBusinessRules();

        $calendarService = app(CalendarServiceContract::class);
        $appointmentDate = Carbon::parse($this->selectedDate . ' ' . $this->selectedTime);
        $clientInfo = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ];

        try {
            $booking = $calendarService->bookAppointment(
                $clientInfo,
                $this->selectedService,
                $appointmentDate
            );

            $booking->update(['notes' => $this->notes]);

            // Send confirmation email
            Mail::to($this->email)->send(new \App\Mail\BookingConfirmation($booking));

            session()->flash('message', 'Booking successful! Check your email for confirmation details.');
            $this->resetForm();

            $this->dispatch('booking-created');
            $this->dispatch('close-booking-form');
        } catch (BookingException $e) {
            session()->flash('error', $e->getMessage());
            $this->loadAvailableTimeSlots();
        } catch (\Exception $e) {
            session()->flash('error', 'An unexpected error occurred. Please try again or contact us directly.');
            Log::error('Booking error', [
                'error' => $e->getMessage(),
                'data' => [
                    'service_id' => $this->selectedService,
                    'date' => $this->selectedDate,
                    'time' => $this->selectedTime,
                    'email' => $this->email
                ]
            ]);
            $this->loadAvailableTimeSlots();
        }
    }

    private function validateBusinessRules()
    {
        // Check if salon is closed on this day (Sunday)
        $dayOfWeek = Carbon::parse($this->selectedDate)->dayOfWeek;
        if ($dayOfWeek === 0) {
            throw BookingException::salonClosed();
        }

        // Check business hours
        $time = Carbon::parse($this->selectedTime);
        $hour = $time->hour;
        if ($hour < 9 || $hour >= 20) {
            throw BookingException::outsideBusinessHours();
        }

        // Check if service is active
        $service = Service::find($this->selectedService);
        if (!$service || !$service->isAvailable()) {
            throw BookingException::invalidService();
        }
    }

    public function resetForm()
    {
        $this->reset(['selectedService', 'selectedDate', 'selectedTime', 'name', 'email', 'phone', 'notes']);
        $this->selectedDate = now()->format('Y-m-d');
        $this->availableTimeSlots = [];
    }

    public function render()
    {
        return view('livewire.booking-form');
    }
}
