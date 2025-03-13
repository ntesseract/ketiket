<?php
// app/Http/Controllers/HotelController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\Review;
use App\Services\LocationService;
use App\Services\RecommendationService;
use Illuminate\Support\Facades\Auth;

class HotelController extends Controller
{
    protected $locationService;
    protected $recommendationService;
    
    public function __construct(LocationService $locationService, RecommendationService $recommendationService)
    {
        $this->locationService = $locationService;
        $this->recommendationService = $recommendationService;
    }
    
    /**
     * Display a listing of hotels
     */
    public function index(Request $request)
    {
        $query = Hotel::query();
        
        // Apply filters
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('min_price')) {
            $query->where('price_per_night', '>=', $request->min_price);
        }
        
        if ($request->has('max_price')) {
            $query->where('price_per_night', '<=', $request->max_price);
        }
        
        if ($request->has('star_rating')) {
            $query->where('star_rating', '=', $request->star_rating);
        }
        
        // Sort results
        $sortBy = $request->sort_by ?? 'name';
        $sortOrder = $request->sort_order ?? 'asc';
        
        $query->orderBy($sortBy, $sortOrder);
        
        // Get paginated results
        $hotels = $query->paginate(12);
        
        return view('hotels.index', compact('hotels'));
    }
    
    /**
     * Display the specified hotel
     */
    public function show(Hotel $hotel)
    {
        // Get reviews
        $reviews = $hotel->reviews()->with('user')->latest()->get();
        
        // Get average rating
        $averageRating = $hotel->averageRating();
        
        // Get map URL
        $mapUrl = null;
        if ($hotel->latitude && $hotel->longitude) {
            $mapUrl = $this->locationService->getStaticMapUrl(
                $hotel->latitude,
                $hotel->longitude
            );
        }
        
        // Get nearby attractions if coordinates are available
        $nearbyAttractions = [];
        if ($hotel->latitude && $hotel->longitude) {
            $nearbyAttractions = $this->locationService->getNearbyAttractions(
                $hotel->latitude,
                $hotel->longitude,
                2.0 // 2km radius
            );
        }
        
        // Get similar hotels
        $similarHotels = Hotel::where('id', '!=', $hotel->id)
            ->where(function ($query) use ($hotel) {
                $query->where('star_rating', '=', $hotel->star_rating)
                      ->orWhere('price_per_night', '>=', $hotel->price_per_night * 0.8)
                      ->orWhere('price_per_night', '<=', $hotel->price_per_night * 1.2);
            })
            ->inRandomOrder()
            ->limit(4)
            ->get();
            
        return view('hotels.show', compact(
            'hotel', 
            'reviews', 
            'averageRating', 
            'mapUrl',
            'nearbyAttractions',
            'similarHotels'
        ));
    }
    
    /**
     * Display AI recommended hotels
     */
    public function recommended(Request $request)
    {
        $user = Auth::user();
        
        // Get recommended hotels based on user's location
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        
        $recommendedHotels = $this->recommendationService->getHotelRecommendations(
            $user,
            $latitude,
            $longitude,
            10
        );
        
        return view('hotels.recommended', compact('recommendedHotels'));
    }
    
    /**
     * Store a new review for a hotel
     */
    public function storeReview(Request $request, Hotel $hotel)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10',
        ]);
        
        $user = Auth::user();
        
        // Check if user has already reviewed this hotel
        $existingReview = Review::where('user_id', $user->id)
            ->where('reviewable_id', $hotel->id)
            ->where('reviewable_type', Hotel::class)
            ->first();
            
        if ($existingReview) {
            // Update existing review
            $existingReview->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
            
            $message = 'Review berhasil diperbarui.';
        } else {
            // Create new review
            Review::create([
                'user_id' => $user->id,
                'reviewable_id' => $hotel->id,
                'reviewable_type' => Hotel::class,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
            
            $message = 'Review berhasil ditambahkan.';
        }
        
        return redirect()->back()->with('success', $message);
    }
}