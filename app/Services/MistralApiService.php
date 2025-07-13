<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MistralApiService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.mistral.ai/v1';

    public function __construct()
    {
        $this->apiKey = config('services.mistral.api_key', '');
    }

    /**
     * Get AI response for salon-related questions
     */
    public function getSalonAnswer(string $question): string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => 'mistral-medium',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getSystemPrompt()
                    ],
                    [
                        'role' => 'user',
                        'content' => $question
                    ]
                ],
                'max_tokens' => 500,
                'temperature' => 0.7
            ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content', 'I apologize, but I couldn\'t process your request at the moment.');
            }

            Log::error('Mistral API error', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return 'I apologize, but I\'m having trouble connecting to my knowledge base right now. Please try again later or contact us directly.';

        } catch (\Exception $e) {
            Log::error('Mistral API exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return 'I apologize, but I\'m experiencing technical difficulties. Please contact us directly for assistance.';
        }
    }

    /**
     * Get system prompt for salon context
     */
    private function getSystemPrompt(): string
    {
        return "You are a helpful AI assistant for Aruma Beauty, a black-owned hair salon in Philadelphia.

        SALON INFORMATION:
        - Services: Hair relaxers, full and partial sew-in weaves, box braids, wash and curl, rod sets, moisturizing treatments, protein reconstructors
        - Location: Philadelphia
        - Specialties: African hair braiding, natural hair care, professional styling

        PRICING (approximate):
        - Wash and Curl: $50
        - Rod Set: $50
        - Full Sew-in Weave: $150
        - Partial Sew-in Weave: $90
        - Moisturizing Treatment: $50
        - Protein Reconstructor: $50
        - Box Braids: $80-150 (depending on length and style)

        BUSINESS HOURS:
        - Monday - Friday: 9:00 AM - 8:00 PM
        - Saturday: 9:00 AM - 6:00 PM
        - Sunday: Close

        RESPONSE GUIDELINES:
        - Be friendly, professional, and helpful
        - Provide accurate information about services and pricing
        - Encourage booking appointments for specific services
        - Keep responses concise but informative
        - If you don't know something specific, suggest contacting the salon directly
        - Always mention that they can book appointments online or call the salon

        IMPORTANT: If someone asks about booking, direct them to use the booking system or call the salon directly.";
    }
}
