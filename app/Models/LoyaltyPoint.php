<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points',
        'total_spent',
        'tier',
        'last_redeemed_at'
    ];

    protected $casts = [
        'last_redeemed_at' => 'datetime'
    ];

    const TIERS = [
        'bronze' => [
            'name' => 'Bronze',
            'points_multiplier' => 1,
            'discount_percentage' => 0
        ],
        'silver' => [
            'name' => 'Silver',
            'points_multiplier' => 1.2,
            'discount_percentage' => 5
        ],
        'gold' => [
            'name' => 'Gold',
            'points_multiplier' => 1.5,
            'discount_percentage' => 10
        ],
        'platinum' => [
            'name' => 'Platinum',
            'points_multiplier' => 2,
            'discount_percentage' => 15
        ]
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addPoints($amount)
    {
        $multiplier = self::TIERS[$this->tier]['points_multiplier'];
        $this->points += $amount * $multiplier;
        $this->total_spent += $amount;
        $this->updateTier();
        $this->save();
    }

    public function updateTier()
    {
        if ($this->total_spent >= 1000) {
            $this->tier = 'platinum';
        } elseif ($this->total_spent >= 500) {
            $this->tier = 'gold';
        } elseif ($this->total_spent >= 200) {
            $this->tier = 'silver';
        } else {
            $this->tier = 'bronze';
        }
    }

    public function getDiscountPercentage()
    {
        return self::TIERS[$this->tier]['discount_percentage'];
    }
}
