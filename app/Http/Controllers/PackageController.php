<?php
// app/Http/Controllers/PackageController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TravelPackage;
use App\Services\RecommendationService;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    protected $recommendationService;
    
    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }
    
    /**
     * Display a listing of travel packages
     */
    public function index(Request $request)
    {
        $query = TravelPackage::query();
        
        // Apply filters
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        if ($request->has('duration')) {
            $query->where('duration_days', '=', $request->duration);
        }
        
        // Sort results
        $sortBy = $request->sort_by ?? 'name';
        $sortOrder = $request->sort_order ?? 'asc';
        
        $query->orderBy($sortBy, $sortOrder);
        
        // Get paginated results with relationships
        $packages = $query->with(['destinations', 'hotels', 'restaurants'])->paginate(9);
        
        // Get unique durations for filter
        $durations = TravelPackage::select('duration_days')
            ->distinct()
            ->orderBy('duration_days')
            ->pluck('duration_days');
        
        return view('packages.index', compact('packages', 'durations'));
    }
    
    /**
     * Display the specified package
     */
    public function show(TravelPackage $package)
    {
        // Load relationships
        $package->load(['destinations', 'hotels', 'restaurants']);
        
        // Get average rating from included destinations, hotels, and restaurants
        $averageRating = $package->getAverageRating();
        
        // Get first destination for map preview
        $mapUrl = null;
        $firstDestination = $package->destinations->first();
        
        if ($firstDestination && $firstDestination->latitude && $firstDestination->longitude) {
            $mapUrl = (new \App\Services\LocationService())->getStaticMapUrl(
                $firstDestination->latitude,
                $firstDestination->longitude
            );
        }
        
        // Count total attractions
        $totalAttractions = $package->getTotalAttractionsCount();
        
        // Get similar packages
        $similarPackages = TravelPackage::where('id', '!=', $package->id)
            ->where(function ($query) use ($package) {
                $query->where('duration_days', '=', $package->duration_days)
                      ->orWhere('price', '>=', $package->price * 0.8)
                      ->orWhere('price', '<=', $package->price * 1.2);
            })
            ->inRandomOrder()
            ->limit(3)
            ->get();
            
        return view('packages.show', compact(
            'package', 
            'averageRating', 
            'mapUrl',
            'totalAttractions',
            'similarPackages'
        ));
    }
    
    /**
     * Display AI recommended package
     */
    public function recommended()
    {
        $user = Auth::user();
        
        $recommendedPackage = $this->recommendationService->getRecommendedPackage($user);
        
        if (!$recommendedPackage) {
            // If no recommendation found, show a random featured package
            $recommendedPackage = TravelPackage::withCount('destinations')
                ->having('destinations_count', '>', 0)
                ->inRandomOrder()
                ->first();
        }
        
        // Get additional packages as alternatives
        $alternativePackages = TravelPackage::where('id', '!=', $recommendedPackage->id)
            ->withCount('destinations')
            ->having('destinations_count', '>', 0)
            ->orderBy('price')
            ->limit(3)
            ->get();
        
        return view('packages.recommended', compact('recommendedPackage', 'alternativePackages'));
    }
    
    /**
     * Display package itinerary details
     */
    public function itinerary(TravelPackage $package)
    {
        // Load relationships
        $package->load(['destinations', 'hotels', 'restaurants']);
        
        // Organize itinerary by day
        $itinerary = [];
        $dayCount = $package->duration_days;
        
        // Simple algorithm to distribute attractions over days
        $destinations = $package->destinations;
        $hotels = $package->hotels;
        $restaurants = $package->restaurants;
        
        for ($day = 1; $day <= $dayCount; $day++) {
            $dayItinerary = [
                'day' => $day,
                'activities' => []
            ];
            
            // Morning: Visit destination
            $destination = $destinations->shift();
            if ($destination) {
                $dayItinerary['activities'][] = [
                    'time' => '09:00',
                    'type' => 'destination',
                    'item' => $destination,
                    'description' => 'Kunjungan ke ' . $destination->name
                ];
            }
            
            // Lunch: Restaurant
            $restaurant = $restaurants->shift();
            if ($restaurant) {
                $dayItinerary['activities'][] = [
                    'time' => '12:30',
                    'type' => 'restaurant',
                    'item' => $restaurant,
                    'description' => 'Makan siang di ' . $restaurant->name
                ];
            }
            
            // Afternoon: Another destination if available
            if (!$destinations->isEmpty()) {
                $destination = $destinations->shift();
                $dayItinerary['activities'][] = [
                    'time' => '14:30',
                    'type' => 'destination',
                    'item' => $destination,
                    'description' => 'Kunjungan ke ' . $destination->name
                ];
            }
            
            // Evening: Hotel check-in or restaurant
            if ($day < $dayCount && !$hotels->isEmpty()) {
                $hotel = $hotels->shift();
                $dayItinerary['activities'][] = [
                    'time' => '18:00',
                    'type' => 'hotel',
                    'item' => $hotel,
                    'description' => 'Check-in dan beristirahat di ' . $hotel->name
                ];
            } elseif (!$restaurants->isEmpty()) {
                $restaurant = $restaurants->shift();
                $dayItinerary['activities'][] = [
                    'time' => '18:00',
                    'type' => 'restaurant',
                    'item' => $restaurant,
                    'description' => 'Makan malam di ' . $restaurant->name
                ];
            }
            
            $itinerary[] = $dayItinerary;
        }
        
        return view('packages.itinerary', compact('package', 'itinerary'));
    }
}