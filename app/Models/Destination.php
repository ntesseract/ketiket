<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'location',
        'latitude',
        'longitude',
        'price',
        'image',
        'capacity',
        'opening_hour',
        'closing_hour',
        'status',
    ];

    /**
     * Get all bookings for this destination
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get all reviews for this destination
     */
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Get users who favorited this destination
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'destination_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Calculate average rating
     */
    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Travel packages that include this destination
     */
    public function travelPackages()
    {
        return $this->belongsToMany(TravelPackage::class, 'package_destinations');
    }
}