<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GiftCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'amount',
        'remaining_amount',
        'purchased_by',
        'recipient_email',
        'recipient_name',
        'message',
        'expires_at',
        'is_used'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean'
    ];

    public static function generateUniqueCode()
    {
        do {
            $code = strtoupper(Str::random(12));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function purchaser()
    {
        return $this->belongsTo(User::class, 'purchased_by');
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isValid()
    {
        return !$this->is_used && !$this->isExpired() && $this->remaining_amount > 0;
    }

    public function use($amount)
    {
        if (!$this->isValid() || $amount > $this->remaining_amount) {
            return false;
        }

        $this->remaining_amount -= $amount;
        if ($this->remaining_amount <= 0) {
            $this->is_used = true;
        }
        $this->save();

        return true;
    }
}