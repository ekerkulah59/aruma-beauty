@component('mail::message')
# Booking Confirmation

Dear {{ $booking->name }},

Thank you for booking with Aruma Beauty. Here are your booking details:

**Service:** {{ $booking->service->name }}
**Date:** {{ $booking->booking_date->format('F j, Y') }}
**Time:** {{ \Carbon\Carbon::parse($booking->booking_time)->format('g:i A') }}

We will review your booking and send you a confirmation shortly. If you need to make any changes, please contact us at (123) 456-7890.

@component('mail::button', ['url' => config('app.url')])
Visit Our Website
@endcomponent

Thank you for choosing Aruma Beauty!

Best regards,<br>
Aruma Beauty Team
@endcomponent
