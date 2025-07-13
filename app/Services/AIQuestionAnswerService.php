<?php

namespace App\Services;

class AIQuestionAnswerService
{
    private MistralApiService $mistralService;

    public function __construct(MistralApiService $mistralService)
    {
        $this->mistralService = $mistralService;
    }

    /**
     * Get answer for salon-related questions
     */
    public function getAnswer(string $question): string
    {
        // Validate question
        if (empty(trim($question))) {
            return 'Please provide a question so I can help you better.';
        }

        // Get AI response
        return $this->mistralService->getSalonAnswer($question);
    }

    /**
     * Check if question is booking-related
     */
    public function isBookingQuestion(string $question): bool
    {
        $bookingKeywords = [
            'book', 'booking', 'appointment', 'schedule', 'reserve', 'make appointment',
            'when can i come', 'available time', 'time slot', 'reservation'
        ];

        $questionLower = strtolower($question);

        foreach ($bookingKeywords as $keyword) {
            if (str_contains($questionLower, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get suggested questions for users
     */
    public function getSuggestedQuestions(): array
    {
        return [
            'What services do you offer?',
            'How much does a partial sew-in cost?',
            'What are your business hours?',
            'Do you do box braids?',
            'How long does a wash and curl take?',
            'What hair treatments do you offer?'
        ];
    }
}
