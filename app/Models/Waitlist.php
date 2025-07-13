<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Waitlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'name',
        'email',
        'phone',
        'preferred_date',
        'preferred_time_slot',
        'notes',
        'status',
        'notified_at'
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'notified_at' => 'datetime'
    ];

    const STATUSES = [
        'waiting' => 'Waiting',
        'notified' => 'Notified',
        'booked' => 'Booked',
        'expired' => 'Expired'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function notify()
    {
        // Send notification to customer
        \Mail::to($this->email)->send(new \App\Mail\WaitlistNotification($this));

        $this->status = 'notified';
        $this->notified_at = now();
        $this->save();
    }

    public function convertToBooking()
    {
        $booking = Booking::create([
            'service_id' => $this->service_id,
            'booking_date' => $this->preferred_date,
            'booking_time' => $this->preferred_time_slot,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'notes' => $this->notes,
            'status' => 'pending'
        ]);

        $this->status = 'booked';
        $this->save();

        return $booking;
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'waiting')
                    ->where('preferred_date', '>=', now());
    }
}
