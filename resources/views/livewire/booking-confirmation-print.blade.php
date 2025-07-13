<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation - Aruma Beauty</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #d6c7b0;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #d6c7b0;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #d6c7b0;
            margin-bottom: 10px;
        }
        .details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .detail-item {
            margin-bottom: 10px;
        }
        .label {
            font-weight: bold;
            color: #666;
        }
        .value {
            color: #333;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
                        <div class="logo">Aruma Beauty</div>
        <h1>Booking Confirmation</h1>
    </div>

    <div class="section">
        <div class="section-title">Appointment Details</div>
        <div class="details">
            <div class="detail-item">
                <span class="label">Service:</span>
                <span class="value">{{ $booking->service->name }}</span>
            </div>
            <div class="detail-item">
                <span class="label">Date:</span>
                <span class="value">{{ $booking->booking_date->format('F j, Y') }}</span>
            </div>
            <div class="detail-item">
                <span class="label">Time:</span>
                <span class="value">{{ \Carbon\Carbon::parse($booking->booking_time)->format('g:i A') }}</span>
            </div>
            <div class="detail-item">
                <span class="label">Duration:</span>
                <span class="value">{{ $booking->service->duration }} minutes</span>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Contact Information</div>
        <div class="details">
            <div class="detail-item">
                <span class="label">Name:</span>
                <span class="value">{{ $booking->name }}</span>
            </div>
            <div class="detail-item">
                <span class="label">Email:</span>
                <span class="value">{{ $booking->email }}</span>
            </div>
            <div class="detail-item">
                <span class="label">Phone:</span>
                <span class="value">{{ $booking->phone }}</span>
            </div>
        </div>
    </div>

    @if($booking->notes)
    <div class="section">
        <div class="section-title">Additional Notes</div>
        <p>{{ $booking->notes }}</p>
    </div>
    @endif

    <div class="footer">
                    <p>Aruma Beauty</p>
        <p>123 Salon Street, City, State 12345</p>
        <p>Phone: (123) 456-7890</p>
        <p>Email: info@hairdujour.com</p>
        <p>Booking Reference: #{{ $booking->id }}</p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Print Confirmation</button>
    </div>
</body>
</html>
