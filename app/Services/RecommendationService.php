<?php

namespace App\Services;

use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    /**
     * Get recommendations for destinations based on user preferences and history
     */
    public function getDestinationRecommendations(User $user, $limit = 5)
    {
        // Get user's booking history
        $userBookings = $user->bookings()->with('destination')->get();
        
        // Get user's reviews
        $userReviews = $user->reviews()
            ->where('reviewable_type', 'App\Models\Destination')
            ->get();
        
        // Get user's favorites
        $userFavorites = $user->favorites()->get();
        
        // Default recommendations (highest rated)
        $defaultRecommendations = Destination::withCount('reviews')
            ->having('reviews_count', '>', 5)
            ->orderBy(DB::raw('(SELECT AVG(rating) FROM reviews WHERE reviewable_id = destinations.id AND reviewable_type = "App\\\Models\\\Destination")'), 'desc')
            ->limit($limit)
            ->get();
            
        // If user has no history, return default recommendations
        if ($userBookings->isEmpty() && $userReviews->isEmpty() && $userFavorites->isEmpty()) {
            return $defaultRecommendations;
        }
        
        // Get destinations with similar categories to user's past bookings and favorites
        $pastDestinationIds = $userBookings->pluck('destination_id')->merge($userFavorites->pluck('id'))->unique()->filter();
        
        // Use collaborative filtering algorithm for recommendations
        if ($pastDestinationIds->isNotEmpty()) {
            // Find users with similar tastes
            $similarUsers = Booking::whereIn('destination_id', $pastDestinationIds)
                ->where('user_id', '!=', $user->id)
                ->distinct()
                ->pluck('user_id');
                
            // Get destinations visited by similar users but not by current user
            $recommendedDestinations = Destination::whereIn('id', function ($query) use ($similarUsers, $pastDestinationIds) {
                $query->select('destination_id')
                    ->from('bookings')
                    ->whereIn('user_id', $similarUsers)
                    ->whereNotIn('destination_id', $pastDestinationIds);
            })
            ->withCount(['reviews' => function ($query) {
                $query->where('rating', '>=', 4);
            }])
            ->orderBy('reviews_count', 'desc')
            ->limit($limit)
            ->get();
                
            if ($recommendedDestinations->count() >= $limit) {
                return $recommendedDestinations;
            }
            
            // If not enough recommendations, mix with default ones
            $remaining = $limit - $recommendedDestinations->count();
            $moreDestinations = Destination::whereNotIn('id', $recommendedDestinations->pluck('id'))
                ->whereNotIn('id', $pastDestinationIds)
                ->withCount('reviews')
                ->having('reviews_count', '>', 0)
                ->orderBy(DB::raw('(SELECT AVG(rating) FROM reviews WHERE reviewable_id = destinations.id AND reviewable_type = "App\\\Models\\\Destination")'), 'desc')
                ->limit($remaining)
                ->get();
                
            return $recommendedDestinations->merge($moreDestinations);
        }
        
        return $defaultRecommendations;
    }
    
    /**
     * Get recommendations for hotels based on user preferences and location
     */
    public function getHotelRecommendations(User $user, ?float $latitude = null, ?float $longitude = null, $limit = 5)
    {
        // Get hotels with best ratings
        $bestHotels = Hotel::withCount('reviews')
            ->having('reviews_count', '>', 3)
            ->orderBy(DB::raw('(SELECT AVG(rating) FROM reviews WHERE reviewable_id = hotels.id AND reviewable_type = "App\\\Models\\\Hotel")'), 'desc')
            ->limit($limit);
            
        // If location is provided, prioritize nearby hotels
        if ($latitude && $longitude) {
            $bestHotels->select('*')
                ->selectRaw(
                    '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                    [$latitude, $longitude, $latitude]
                )
                ->orderBy('distance');
        }
        
        return $bestHotels->get();
    }
    
    /**
     * Get recommendations for restaurants based on user preferences and location
     */
    public function getRestaurantRecommendations(User $user, ?float $latitude = null, ?float $longitude = null, $limit = 5)
    {
        // Get restaurants with best ratings
        $bestRestaurants = Restaurant::withCount('reviews')
            ->having('reviews_count', '>', 3)
            ->orderBy(DB::raw('(SELECT AVG(rating) FROM reviews WHERE reviewable_id = restaurants.id AND reviewable_type = "App\\\Models\\\Restaurant")'), 'desc')
            ->limit($limit);
            
        // If location is provided, prioritize nearby restaurants
        if ($latitude && $longitude) {
            $bestRestaurants->select('*')
                ->selectRaw(
                    '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                    [$latitude, $longitude, $latitude]
                )
                ->orderBy('distance');
        }
        
        return $bestRestaurants->get();
    }
    
    /**
     * Get a recommended travel package based on user preferences
     */
    public function getRecommendedPackage(User $user)
    {
        // Get user's favorite destinations
        $favoriteDestinations = $user->favorites()->get();
        
        // Get user's high-rated reviews
        $highRatedDestinations = Review::where('user_id', $user->id)
            ->where('reviewable_type', 'App\Models\Destination')
            ->where('rating', '>=', 4)
            ->get()
            ->pluck('reviewable_id');
        
        // Look for packages that contain user's favorite or highly rated destinations
        if ($favoriteDestinations->isNotEmpty() || $highRatedDestinations->isNotEmpty()) {
            $destinationIds = $favoriteDestinations->pluck('id')
                ->merge($highRatedDestinations)
                ->unique();
                
            $package = \App\Models\TravelPackage::whereHas('destinations', function ($query) use ($destinationIds) {
                $query->whereIn('destinations.id', $destinationIds);
            })
            ->first();
            
            if ($package) {
                return $package;
            }
        }
        
        // Default to highest-rated package
        return \App\Models\TravelPackage::withCount('destinations')
            ->having('destinations_count', '>', 0)
            ->orderBy(function ($query) {
                $query->selectRaw('AVG(r.rating)')
                    ->from('reviews as r')
                    ->join('package_destinations as pd', 'pd.destination_id', '=', 'r.reviewable_id')
                    ->where('r.reviewable_type', 'App\Models\Destination')
                    ->whereRaw('pd.travel_package_id = travel_packages.id');
            }, 'desc')
            ->first();
    }
}