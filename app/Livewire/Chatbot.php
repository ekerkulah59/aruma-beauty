<?php

namespace App\Livewire;

use App\Models\Service;
use App\Services\AIQuestionAnswerService;
use App\Services\BookingFlowManager;
use App\Services\CalendarServiceContract;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Chatbot extends Component
{
    // Chat properties
    public $showChat = false;
    public $messages = [];
    public $userInput = '';
    public $isTyping = false;
    public $bookingData = [];
    public $bookingOptions = [];
    public $currentStep = 'idle';

    // Services
    private AIQuestionAnswerService $aiService;
    private CalendarServiceContract $calendarService;
    private BookingFlowManager $bookingFlowManager;

    protected $listeners = ['toggleChat' => 'toggleChat'];

    public function toggleChat()
    {
        $this->showChat = !$this->showChat;
        if ($this->showChat && empty($this->messages)) {
            $this->startConversation();
        }
    }

    public function boot(
        AIQuestionAnswerService $aiService,
        CalendarServiceContract $calendarService,
        BookingFlowManager $bookingFlowManager
    ) {
        $this->aiService = $aiService;
        $this->calendarService = $calendarService;
        $this->bookingFlowManager = $bookingFlowManager;
    }

    private function startConversation()
    {
        if (empty($this->messages)) {
            $this->addBotMessage("Hi! I'm your ARUMA Hair Salon assistant. I can help with questions or book an appointment. What can I do for you today?");
            $this->addSuggestedQuestions();
        }
    }

    public function sendMessage()
    {
        $input = trim($this->userInput);
        if ($input === '') {
            return;
        }

        $this->addUserMessage($input);
        $this->userInput = '';
        $this->isTyping = true;
        $this->dispatch('scroll-to-bottom');

        $this->handleConversation($input);

        $this->isTyping = false;
        $this->dispatch('scroll-to-bottom');
    }

    private function handleConversation(string $input)
    {
        $lowerInput = strtolower($input);
        $bookingKeywords = ['book', 'booking', 'appointment', 'schedule'];

        $isBookingRequest = false;
        foreach ($bookingKeywords as $keyword) {
            if (str_contains($lowerInput, $keyword)) {
                $isBookingRequest = true;
                break;
            }
        }

        if ($this->currentStep === 'idle' && $isBookingRequest) {
            $this->currentStep = 'booking_name';
            $message = $this->bookingFlowManager->generateStepMessage($this->currentStep);
            $this->addBotMessage($message);
            return;
        }

        // Handle booking flow steps
        if ($this->currentStep !== 'idle') {
            $this->handleBookingStep($input);
            return;
        }

        // Default to AI response for general questions
        $this->getAIResponse($input);
    }

    private function handleBookingStep(string $input)
    {
        // Handle basic data collection steps
        if (in_array($this->currentStep, ['booking_name', 'booking_email', 'booking_phone'])) {
            $validation = $this->bookingFlowManager->validateStepInput($this->currentStep, $input);

            if (!$validation['valid']) {
                $this->addBotMessage($validation['message']);
                return;
            }

            // Store the validated data
            $dataKey = str_replace('booking_', '', $this->currentStep);
            $this->bookingData[$dataKey] = $validation['data'];

            // Move to next step
            $this->currentStep = $this->bookingFlowManager->getNextStep($this->currentStep, $this->bookingData);
            $message = $this->bookingFlowManager->generateStepMessage($this->currentStep, $this->bookingData);
            $this->addBotMessage($message);
            return;
        }

        // Handle selection-based steps
        match($this->currentStep) {
            'booking_service' => $this->handleServiceSelection($input),
            'booking_date' => $this->handleDateSelection($input),
            'booking_time' => $this->handleTimeSelection($input),
            'booking_confirm' => $this->handleBookingConfirmation($input),
            default => $this->getAIResponse($input)
        };
    }

    private function handleServiceSelection(string $input)
    {
        $validation = $this->bookingFlowManager->validateStepInput($this->currentStep, $input);

        if (!$validation['valid']) {
            $this->addBotMessage($validation['message']);
            $message = $this->bookingFlowManager->generateStepMessage($this->currentStep);
            $this->addBotMessage($message);
            return;
        }

        $this->bookingData['service_id'] = $validation['data'];
        $this->currentStep = $this->bookingFlowManager->getNextStep($this->currentStep, $this->bookingData);

        // Get available dates and set up options
        $dates = $this->calendarService->getAvailableDates($this->bookingData['service_id']);
        if (empty($dates)) {
            $this->resetConversation("I'm sorry, there are no upcoming available dates for that service. Please try another service or contact us directly.");
            return;
        }

        $this->bookingOptions = [];
        foreach ($dates as $index => $date) {
            $this->bookingOptions[$index + 1] = $date;
        }

        $message = $this->bookingFlowManager->generateStepMessage($this->currentStep, $this->bookingData);
        $this->addBotMessage($message);
    }

    private function handleDateSelection(string $input)
    {
        $selection = (int)trim($input);
        if (isset($this->bookingOptions[$selection])) {
            $selectedDate = $this->bookingOptions[$selection];
            $this->bookingData['date'] = $selectedDate;
            $this->bookingOptions = [];
            $this->currentStep = $this->bookingFlowManager->getNextStep($this->currentStep, $this->bookingData);

            // Get available slots and set up options
            $slots = $this->calendarService->getAvailableSlots($selectedDate, $this->bookingData['service_id']);
            if (empty($slots)) {
                $this->addBotMessage("Apologies, it looks like there are no more slots on that day. Please select another date.");
                $this->currentStep = 'booking_date';
                $message = $this->bookingFlowManager->generateStepMessage($this->currentStep, $this->bookingData);
                $this->addBotMessage($message);
                return;
            }

            $this->bookingOptions = [];
            foreach ($slots as $index => $slot) {
                $this->bookingOptions[$index + 1] = $slot;
            }

            $message = $this->bookingFlowManager->generateStepMessage($this->currentStep, $this->bookingData);
            $this->addBotMessage($message);
        } else {
            $this->addBotMessage("That's not a valid date option. Please pick a number from the list.");
        }
    }

    private function handleTimeSelection(string $input)
    {
        $selection = (int)trim($input);
        if (isset($this->bookingOptions[$selection])) {
            $selectedSlot = $this->bookingOptions[$selection];
            $this->bookingData['time'] = $selectedSlot;
            $this->bookingOptions = [];
            $this->currentStep = $this->bookingFlowManager->getNextStep($this->currentStep, $this->bookingData);

            $message = $this->bookingFlowManager->generateStepMessage($this->currentStep, $this->bookingData);
            $this->addBotMessage($message);
        } else {
            $this->addBotMessage("I couldn't match that time. Please pick a number from the list.");
        }
    }

    private function handleBookingConfirmation(string $input)
    {
        $validation = $this->bookingFlowManager->validateStepInput($this->currentStep, $input);

        if (!$validation['valid']) {
            $this->addBotMessage($validation['message']);
            return;
        }

        if ($validation['data']) {
            // User confirmed - process the booking
            $result = $this->bookingFlowManager->processBooking($this->bookingData);

            if ($result['success']) {
                $this->addBotMessage($result['message']);
                $this->resetConversation();
            } else {
                $this->addBotMessage($result['message']);
                $this->addBotMessage("Let's try selecting a time again.");
                $this->currentStep = 'booking_time';
                $message = $this->bookingFlowManager->generateStepMessage($this->currentStep, $this->bookingData);
                $this->addBotMessage($message);
            }
        } else {
            // User declined
            $this->addBotMessage("Okay, let's start over. What can I help you with?");
            $this->resetConversation();
            $this->addSuggestedQuestions();
        }
    }

    private function getAIResponse(string $question)
    {
        if (trim($question) === '') {
            return;
        }

        try {
            $response = $this->aiService->getAnswer($question);
            $this->addBotMessage($response);
        } catch (\Exception $e) {
            Log::error('AI Service Error', ['error' => $e->getMessage()]);
            $this->addBotMessage("I'm sorry, I'm having trouble understanding that right now. Could you try rephrasing your question?");
        }
    }

    public function selectSuggestion(string $question)
    {
        $this->userInput = $question;
        $this->sendMessage();
    }

    private function resetConversation(string $message = null)
    {
        $this->currentStep = 'idle';
        $this->bookingData = [];
        $this->bookingOptions = [];

        if ($message) {
            $this->addBotMessage($message);
        }
    }

    private function addUserMessage(string $message)
    {
        $this->messages[] = ['type' => 'user', 'content' => $message, 'timestamp' => now()];
    }

    private function addBotMessage(string $message)
    {
        $this->messages[] = ['type' => 'bot', 'content' => $message, 'timestamp' => now()];
    }

    private function addSuggestedQuestions()
    {
        $this->messages[] = [
            'type' => 'suggestions',
            'content' => ['What services do you offer?', 'What are your hours?', 'I want to book an appointment'],
            'timestamp' => now()
        ];
    }

    public function render()
    {
        return view('livewire.chatbot');
    }
}
