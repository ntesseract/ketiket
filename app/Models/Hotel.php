<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'location',
        'latitude',
        'longitude',
        'price_per_night',
        'image',
        'star_rating',
    ];

    /**
     * Get all bookings for this hotel
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get all reviews for this hotel
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
     * Travel packages that include this hotel
     */
    public function travelPackages()
    {
        return $this->belongsToMany(TravelPackage::class, 'package_hotels');
    }
}