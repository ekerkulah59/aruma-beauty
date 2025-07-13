<?php

namespace App\Services;

use App\Models\Service;
use Carbon\Carbon;

class BookingFlowManager
{
    private CalendarServiceContract $calendarService;

    public function __construct(CalendarServiceContract $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    /**
     * Get the next step in the booking flow based on current step and data
     */
    public function getNextStep(string $currentStep, array $bookingData): string
    {
        return match($currentStep) {
            'idle' => 'booking_name',
            'booking_name' => 'booking_email',
            'booking_email' => 'booking_phone',
            'booking_phone' => 'booking_service',
            'booking_service' => 'booking_date',
            'booking_date' => 'booking_time',
            'booking_time' => 'booking_confirm',
            'booking_confirm' => 'idle',
            default => 'idle'
        };
    }

    /**
     * Validate input for a specific step
     */
    public function validateStepInput(string $step, string $input): array
    {
        return match($step) {
            'booking_email' => $this->validateEmail($input),
            'booking_phone' => $this->validatePhone($input),
            'booking_service' => $this->validateServiceSelection($input),
            'booking_date' => $this->validateDateSelection($input),
            'booking_time' => $this->validateTimeSelection($input),
            'booking_confirm' => $this->validateConfirmation($input),
            default => ['valid' => true, 'data' => $input]
        };
    }

    /**
     * Generate response message for a step
     */
    public function generateStepMessage(string $step, array $bookingData = [], array $options = []): string
    {
        return match($step) {
            'booking_name' => "Great! Let's get you booked. First, what's your full name?",
            'booking_email' => "Thanks, {$bookingData['name']}. What's your email address?",
            'booking_phone' => "And your phone number?",
            'booking_service' => $this->generateServiceSelectionMessage(),
            'booking_date' => $this->generateDateSelectionMessage($bookingData['service_id']),
            'booking_time' => $this->generateTimeSelectionMessage($bookingData['date'], $bookingData['service_id']),
            'booking_confirm' => $this->generateConfirmationMessage($bookingData),
            default => "I'm not sure what you're looking for. Can you please clarify?"
        };
    }

    /**
     * Process booking confirmation and create the appointment
     */
    public function processBooking(array $bookingData): array
    {
        try {
            $clientInfo = [
                'name' => $bookingData['name'],
                'email' => $bookingData['email'],
                'phone' => $bookingData['phone'],
            ];

            $booking = $this->calendarService->bookAppointment(
                $clientInfo,
                $bookingData['service_id'],
                $bookingData['time']
            );

            return [
                'success' => true,
                'message' => "Excellent! Your appointment is confirmed. We've sent a confirmation to {$bookingData['email']}. We look forward to seeing you!",
                'booking' => $booking
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "Oh no, something went wrong while confirming: " . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    private function validateEmail(string $input): array
    {
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => true, 'data' => $input];
        }
        return ['valid' => false, 'message' => "That doesn't look like a valid email. Please try again."];
    }

    private function validatePhone(string $input): array
    {
        // Basic phone validation - you might want to make this more robust
        $cleaned = preg_replace('/[^\d]/', '', $input);
        if (strlen($cleaned) >= 10) {
            return ['valid' => true, 'data' => $input];
        }
        return ['valid' => false, 'message' => "Please enter a valid phone number."];
    }

    private function validateServiceSelection(string $input): array
    {
        $selection = (int)trim($input);
        $services = Service::all();

        if ($selection >= 1 && $selection <= $services->count()) {
            $service = $services->skip($selection - 1)->first();
            return ['valid' => true, 'data' => $service->id, 'service' => $service];
        }

        return ['valid' => false, 'message' => "I'm sorry, that's not a valid option. Please choose a number from the list."];
    }

    private function validateDateSelection(string $input): array
    {
        $selection = (int)trim($input);
        if ($selection >= 1) {
            return ['valid' => true, 'data' => $selection];
        }
        return ['valid' => false, 'message' => "That's not a valid date option. Please pick a number from the list."];
    }

    private function validateTimeSelection(string $input): array
    {
        $selection = (int)trim($input);
        if ($selection >= 1) {
            return ['valid' => true, 'data' => $selection];
        }
        return ['valid' => false, 'message' => "That's not a valid time option. Please pick a number from the list."];
    }

    private function validateConfirmation(string $input): array
    {
        $lowerInput = strtolower(trim($input));
        if (in_array($lowerInput, ['yes', 'y'])) {
            return ['valid' => true, 'data' => true];
        } elseif (in_array($lowerInput, ['no', 'n'])) {
            return ['valid' => true, 'data' => false];
        }
        return ['valid' => false, 'message' => "Please answer with 'yes' or 'no'."];
    }

    private function generateServiceSelectionMessage(): string
    {
        $message = "Great! Which service would you like to book?\n\n";
        $services = Service::all();

        foreach ($services as $index => $service) {
            $optionNum = $index + 1;
            $message .= "{$optionNum}. {$service->name} - \${$service->price} ({$service->duration} min)\n";
        }

        $message .= "\nPlease reply with the number of the service you want.";
        return $message;
    }

    private function generateDateSelectionMessage(int $serviceId): string
    {
        $dates = $this->calendarService->getAvailableDates($serviceId);
        if (empty($dates)) {
            return "I'm sorry, there are no upcoming available dates for that service. Please try another service or contact us directly.";
        }

        $message = "Sounds good. Here are some available dates. Please select a number:\n\n";
        foreach ($dates as $index => $date) {
            $optionNum = $index + 1;
            $message .= "{$optionNum}. " . $date->format('l, F jS') . "\n";
        }
        return $message;
    }

    private function generateTimeSelectionMessage(Carbon $date, int $serviceId): string
    {
        $slots = $this->calendarService->getAvailableSlots($date, $serviceId);
        if (empty($slots)) {
            return "Apologies, it looks like there are no more slots on that day. Please select another date.";
        }

        $message = "Perfect. Here are the available times for *{$date->format('F jS')}*. Please select a number:\n\n";
        foreach ($slots as $index => $slot) {
            $optionNum = $index + 1;
            $message .= "{$optionNum}. " . $slot->format('g:i A') . "\n";
        }
        return $message;
    }

    private function generateConfirmationMessage(array $bookingData): string
    {
        $service = Service::find($bookingData['service_id']);
        $date = $bookingData['time']->format('l, F jS, Y');
        $time = $bookingData['time']->format('g:i A');

        return "Awesome! Please confirm your appointment details:\n\n" .
            "• *Name:* " . $bookingData['name'] . "\n" .
            "• *Email:* " . $bookingData['email'] . "\n" .
            "• *Service:* " . $service->name . "\n" .
            "• *Date:* " . $date . "\n" .
            "• *Time:* " . $time . "\n\n" .
            "Does this look right? (yes/no)";
    }
}
