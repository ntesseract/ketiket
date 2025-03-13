<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_days',
        'image',
    ];

    /**
     * Get destinations included in this package
     */
    public function destinations()
    {
        return $this->belongsToMany(Destination::class, 'package_destinations');
    }

    /**
     * Get hotels included in this package
     */
    public function hotels()
    {
        return $this->belongsToMany(Hotel::class, 'package_hotels');
    }

    /**
     * Get restaurants included in this package
     */
    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'package_restaurants');
    }
    
    /**
     * Create a booking for this package
     */
    public function createBooking($userId, $visitDate, $numberOfTickets)
    {
        // Get first destination, hotel, and restaurant
        $destination = $this->destinations()->first();
        $hotel = $this->hotels()->first();
        $restaurant = $this->restaurants()->first();
        
        if (!$destination || !$hotel || !$restaurant) {
            throw new \Exception('Package is incomplete. Must have at least one destination, hotel, and restaurant.');
        }
        
        // Calculate total price
        $totalPrice = $this->price * $numberOfTickets;
        
        // Create booking
        $booking = Booking::create([
            'user_id' => $userId,
            'destination_id' => $destination->id,
            'hotel_id' => $hotel->id,
            'restaurant_id' => $restaurant->id,
            'visit_date' => $visitDate,
            'number_of_tickets' => $numberOfTickets,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'is_package' => true,
        ]);
        
        return $booking;
    }
    
    /**
     * Get total attractions count
     */
    public function getTotalAttractionsCount()
    {
        return $this->destinations->count() + $this->hotels->count() + $this->restaurants->count();
    }
    
    /**
     * Calculate average rating from destinations, hotels, and restaurants
     */
    public function getAverageRating()
    {
        $destinationsRating = $this->destinations->avg(function($destination) {
            return $destination->averageRating();
        });
        
        $hotelsRating = $this->hotels->avg(function($hotel) {
            return $hotel->averageRating();
        });
        
        $restaurantsRating = $this->restaurants->avg(function($restaurant) {
            return $restaurant->averageRating();
        });
        
        $totalRating = ($destinationsRating + $hotelsRating + $restaurantsRating) / 3;
        
        return round($totalRating, 1);
    }
}