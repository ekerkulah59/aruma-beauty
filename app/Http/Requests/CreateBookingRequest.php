<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Anyone can create a booking
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'service_id' => [
                'required',
                'integer',
                Rule::exists('services', 'id')->where('active', true)
            ],
            'booking_date' => [
                'required',
                'date',
                'after:today',
                function ($attribute, $value, $fail) {
                    // Check if salon is closed on this day (Sunday)
                    $dayOfWeek = \Carbon\Carbon::parse($value)->dayOfWeek;
                    if ($dayOfWeek === 0) {
                        $fail('We are closed on Sundays. Please select another day.');
                    }
                }
            ],
            'booking_time' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $time = \Carbon\Carbon::parse($value);
                    $hour = $time->hour;

                    // Check business hours (9 AM - 8 PM)
                    if ($hour < 9 || $hour >= 20) {
                        $fail('Please select a time between 9:00 AM and 8:00 PM.');
                    }
                }
            ],
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-Z\s\'-]+$/' // Only letters, spaces, hyphens, apostrophes
            ],
            'email' => [
                'required',
                'email',
                'max:255'
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^[\+]?[1-9][\d]{0,15}$/' // International phone format
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000'
            ]
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'service_id.required' => 'Please select a service.',
            'service_id.exists' => 'The selected service is not available.',
            'booking_date.required' => 'Please select a date for your appointment.',
            'booking_date.after' => 'Cannot book appointments in the past.',
            'booking_time.required' => 'Please select a time for your appointment.',
            'booking_time.date_format' => 'Please select a valid time format.',
            'name.required' => 'Please provide your full name.',
            'name.regex' => 'Name can only contain letters, spaces, hyphens, and apostrophes.',
            'email.required' => 'Please provide your email address.',
            'email.email' => 'Please provide a valid email address.',
            'phone.required' => 'Please provide your phone number.',
            'phone.regex' => 'Please provide a valid phone number.',
            'notes.max' => 'Notes cannot exceed 1000 characters.'
        ];
    }

    /**
     * Get custom attributes for validation error messages.
     */
    public function attributes(): array
    {
        return [
            'service_id' => 'service',
            'booking_date' => 'appointment date',
            'booking_time' => 'appointment time',
            'name' => 'full name',
            'email' => 'email address',
            'phone' => 'phone number',
            'notes' => 'additional notes'
        ];
    }
}
