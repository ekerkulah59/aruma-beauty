<?php

namespace App\Exceptions;

use Exception;

class BookingException extends Exception
{
    public static function slotUnavailable(): self
    {
        return new self('The selected time slot is no longer available. Please choose another time.');
    }

    public static function invalidService(): self
    {
        return new self('The selected service is not available. Please select a valid service.');
    }

    public static function outsideBusinessHours(): self
    {
        return new self('The selected time is outside our business hours. Please choose a time between 9:00 AM and 8:00 PM.');
    }

    public static function dateInPast(): self
    {
        return new self('Cannot book appointments in the past. Please select a future date.');
    }

    public static function overlappingAppointment(): self
    {
        return new self('This time conflicts with an existing appointment. Please choose another time.');
    }

    public static function salonClosed(): self
    {
        return new self('We are closed on this day. Please select another date.');
    }

    public static function invalidClientData(): self
    {
        return new self('Please provide valid contact information (name, email, and phone number).');
    }
}
