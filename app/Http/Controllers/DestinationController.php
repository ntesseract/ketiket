<?php
// app/Http/Controllers/DestinationController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\Review;
use App\Models\Favorite;
use App\Services\LocationService;
use App\Services\RecommendationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DestinationController extends Controller
{
    protected $locationService;
    protected $recommendationService;
    
    public function __construct(LocationService $locationService, RecommendationService $recommendationService)
    {
        $this->locationService = $locationService;
        $this->recommendationService = $recommendationService;
    }
    
    /**
     * Display a listing of destinations
     */
    public function index(Request $request)
    {
        $query = Destination::query();
        
        // Apply filters
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        // Sort results
        $sortBy = $request->sort_by ?? 'name';
        $sortOrder = $request->sort_order ?? 'asc';
        
        $query->orderBy($sortBy, $sortOrder);
        
        // Get paginated results
        $destinations = $query->paginate(12);
        
        return view('destinations.index', compact('destinations'));
    }
    
    /**
     * Display the specified destination
     */
    public function show(Destination $destination)
    {
        $user = Auth::user();
        
        // Check if user has favorited this destination
        $isFavorite = false;
        if ($user) {
            // Menggunakan query langsung untuk memeriksa favorit
            $isFavorite = DB::table('favorites')
                ->where('user_id', $user->id)
                ->where('destination_id', $destination->id)
                ->exists();
        }
        
        // Get reviews
        $reviews = $destination->reviews()->with('user')->latest()->get();
        
        // Get average rating
        $averageRating = $destination->averageRating();
        
        // Get map URL
        $mapUrl = null;
        if ($destination->latitude && $destination->longitude) {
            $mapUrl = $this->locationService->getStaticMapUrl(
                $destination->latitude,
                $destination->longitude
            );
        }
        
        // Get nearby attractions if coordinates are available
        $nearbyAttractions = [];
        if ($destination->latitude && $destination->longitude) {
            $nearbyAttractions = $this->locationService->getNearbyAttractions(
                $destination->latitude,
                $destination->longitude,
                5.0 // 5km radius
            );
        }
        
        // Get similar destinations
        $similarDestinations = Destination::where('id', '!=', $destination->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();
            
        return view('destinations.show', compact(
            'destination', 
            'isFavorite', 
            'reviews', 
            'averageRating', 
            'mapUrl',
            'nearbyAttractions',
            'similarDestinations'
        ));
    }
    
    /**
     * Toggle favorite status of a destination
     */
    public function toggleFavorite(Destination $destination)
    {
        $user = Auth::user();
        
        // Menggunakan query builder untuk memeriksa status favorit
        $exists = DB::table('favorites')
            ->where('user_id', $user->id)
            ->where('destination_id', $destination->id)
            ->exists();
            
        if ($exists) {
            // Hapus dari favorit
            DB::table('favorites')
                ->where('user_id', $user->id)
                ->where('destination_id', $destination->id)
                ->delete();
                
            $message = 'Destinasi dihapus dari favorit.';
        } else {
            // Tambahkan ke favorit
            DB::table('favorites')->insert([
                'user_id' => $user->id,
                'destination_id' => $destination->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $message = 'Destinasi ditambahkan ke favorit.';
        }
        
        return redirect()->back()->with('success', $message);
    }
    
    /**
     * Display list of user's favorite destinations
     */
    public function favorites()
    {
        $user = Auth::user();
        
        // Menggunakan query builder untuk mendapatkan daftar favorit
        $favoriteIds = DB::table('favorites')
            ->where('user_id', $user->id)
            ->pluck('destination_id');
            
        $favorites = Destination::whereIn('id', $favoriteIds)
            ->paginate(12);
        
        return view('destinations.favorites', compact('favorites'));
    }
    
    /**
     * Display AI recommended destinations
     */
    public function recommended()
    {
        $user = Auth::user();
        $recommendedDestinations = $this->recommendationService->getDestinationRecommendations($user, 10);
        
        return view('destinations.recommended', compact('recommendedDestinations'));
    }
    
    /**
     * Store a new review for a destination
     */
    public function storeReview(Request $request, Destination $destination)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10',
        ]);
        
        $user = Auth::user();
        
        // Check if user has already reviewed this destination
        $existingReview = Review::where('user_id', $user->id)
            ->where('reviewable_id', $destination->id)
            ->where('reviewable_type', Destination::class)
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
                'reviewable_id' => $destination->id,
                'reviewable_type' => Destination::class,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
            
            $message = 'Review berhasil ditambahkan.';
        }
        
        return redirect()->back()->with('success', $message);
    }
}