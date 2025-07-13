<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'duration',
        'discount_percentage',
        'is_featured'
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'package_service')
            ->withPivot('order')
            ->orderBy('order');
    }

    public function getTotalDurationAttribute()
    {
        return $this->services->sum('duration');
    }

    public function getDiscountedPriceAttribute()
    {
        $originalPrice = $this->services->sum('price');
        return $originalPrice * (1 - ($this->discount_percentage / 100));
    }
}
