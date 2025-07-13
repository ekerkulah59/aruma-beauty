<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_RESCHEDULED = 'rescheduled';
    public const STATUS_NO_SHOW = 'no_show';

    protected $fillable = [
        'service_id',
        'booking_date',
        'booking_time',
        'name',
        'email',
        'phone',
        'notes',
        'status'
    ];

    protected $casts = [
        'booking_date' => 'datetime',
    ];

    protected $attributes = [
        'status' => self::STATUS_CONFIRMED,
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get all available status options
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_RESCHEDULED => 'Rescheduled',
            self::STATUS_NO_SHOW => 'No Show',
        ];
    }

    /**
     * Check if booking is active (not cancelled)
     */
    public function isActive(): bool
    {
        return $this->status !== self::STATUS_CANCELLED;
    }

    /**
     * Check if booking can be modified
     */
    public function canBeModified(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
            self::STATUS_RESCHEDULED
        ]);
    }
}
