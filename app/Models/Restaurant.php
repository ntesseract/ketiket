<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'location',
        'latitude',
        'longitude',
        'cuisine_type',
        'image',
        'opening_hour',
        'closing_hour',
        'has_vegetarian_options',
    ];

    /**
     * Get all bookings for this restaurant
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get all reviews for this restaurant
     */
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Calculate average rating
     */
    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Travel packages that include this restaurant
     */
    public function travelPackages()
    {
        return $this->belongsToMany(TravelPackage::class, 'package_restaurants');
    }
}