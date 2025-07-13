@component('mail::message')
# Appointment Rescheduled

Dear {{ $booking->name }},

Your appointment with Aruma Beauty has been rescheduled. Here are your updated booking details:

**Service:** {{ $booking->service->name }}
**New Date:** {{ $booking->booking_date->format('F j, Y') }}
**New Time:** {{ \Carbon\Carbon::parse($booking->booking_time)->format('g:i A') }}

If you need to make any further changes, please contact us at (123) 456-7890.

@component('mail::button', ['url' => config('app.url')])
Visit Our Website
@endcomponent

Thank you for choosing Aruma Beauty!

Best regards,<br>
Aruma Beauty Team
@endcomponent
