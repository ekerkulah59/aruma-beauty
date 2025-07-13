@component('mail::message')
# Appointment Cancelled

Dear {{ $booking->name }},

Your appointment with Aruma Beauty has been cancelled.

**Cancelled Appointment Details:**
- Service: {{ $booking->service->name }}
- Date: {{ $booking->booking_date->format('F j, Y') }}
- Time: {{ \Carbon\Carbon::parse($booking->booking_time)->format('g:i A') }}

If you would like to book a new appointment, please visit our website or contact us at (123) 456-7890.

@component('mail::button', ['url' => config('app.url')])
Book New Appointment
@endcomponent

We hope to see you again soon!

Best regards,<br>
Aruma Beauty Team
@endcomponent
