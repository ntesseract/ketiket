<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Review;
use App\Services\LocationService;
use App\Services\RecommendationService;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends Controller
{
    protected $locationService;
    protected $recommendationService;
    
    public function __construct(LocationService $locationService, RecommendationService $recommendationService)
    {
        // Di Laravel 12, kita harus menggunakan nama middleware yang terdaftar di kernel
        // Middleware dapat diakses langsung tanpa menggunakan $this->middleware
        $this->locationService = $locationService;
        $this->recommendationService = $recommendationService;
    }
    
    /**
     * Display a listing of restaurants
     */
    public function index(Request $request)
    {
        $query = Restaurant::where('status', 'active');
        
        // Apply filters
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%')
                  ->orWhere('cuisine_type', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->has('cuisine_type')) {
            $query->where('cuisine_type', 'like', '%' . $request->cuisine_type . '%');
        }
        
        if ($request->has('vegetarian') && $request->vegetarian == '1') {
            $query->where('has_vegetarian_options', true);
        }
        
        // Sort results
        $sortBy = $request->sort_by ?? 'name';
        $sortOrder = $request->sort_order ?? 'asc';
        
        $query->orderBy($sortBy, $sortOrder);
        
        // Get paginated results
        $restaurants = $query->paginate(12);
        
        // Get all cuisine types for filter
        $cuisineTypes = Restaurant::select('cuisine_type')
            ->distinct()
            ->whereNotNull('cuisine_type')
            ->pluck('cuisine_type');
        
        return view('restaurants.index', compact('restaurants', 'cuisineTypes'));
    }
    
    /**
     * Display the specified restaurant
     */
    public function show(Restaurant $restaurant)
    {
        // Ensure only active restaurants are shown
        if ($restaurant->status !== 'active') {
            abort(404);
        }

        // Get reviews
        $reviews = $restaurant->reviews()->with('user')->latest()->paginate(10);
        
        // Get average rating
        $averageRating = $restaurant->averageRating();
        
        // Get map URL
        $mapUrl = null;
        if ($restaurant->latitude && $restaurant->longitude) {
            $mapUrl = $this->locationService->getStaticMapUrl(
                $restaurant->latitude,
                $restaurant->longitude
            );
        }
        
        // Format opening hours
        $openingHours = null;
        $closingHours = null;
        
        if ($restaurant->opening_hour) {
            $openingHours = date('H:i', strtotime($restaurant->opening_hour));
        }
        
        if ($restaurant->closing_hour) {
            $closingHours = date('H:i', strtotime($restaurant->closing_hour));
        }
        
        // Check if restaurant is currently open
        $isOpen = false;
        if ($openingHours && $closingHours) {
            $currentTime = date('H:i');
            $isOpen = ($currentTime >= $openingHours && $currentTime <= $closingHours);
        }
        
        // Get similar restaurants
        $similarRestaurants = Restaurant::where('id', '!=', $restaurant->id)
            ->where('status', 'active')
            ->where(function ($query) use ($restaurant) {
                if ($restaurant->cuisine_type) {
                    $query->where('cuisine_type', 'like', '%' . $restaurant->cuisine_type . '%');
                }
            })
            ->inRandomOrder()
            ->limit(4)
            ->get();
            
        // Check if current user has already reviewed
        $userReview = null;
        if (Auth::check()) {
            $userReview = Review::where('user_id', Auth::id())
                ->where('reviewable_id', $restaurant->id)
                ->where('reviewable_type', Restaurant::class)
                ->first();
        }
            
        return view('restaurants.show', compact(
            'restaurant', 
            'reviews', 
            'averageRating', 
            'mapUrl',
            'openingHours',
            'closingHours',
            'isOpen',
            'similarRestaurants',
            'userReview'
        ));
    }
    
    /**
     * Display AI recommended restaurants
     */
    public function recommended(Request $request)
    {
        $user = Auth::user();
        
        // Get recommended restaurants based on user's location
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        
        $recommendedRestaurants = $this->recommendationService->getRestaurantRecommendations(
            $user,
            $latitude,
            $longitude,
            10
        );
        
        return view('restaurants.recommended', compact('recommendedRestaurants'));
    }
    
    /**
     * Store a new review for a restaurant
     */
    public function storeReview(Request $request, Restaurant $restaurant)
    {
        // Ensure restaurant is active
        if ($restaurant->status !== 'active') {
            abort(403, 'Restaurant is not available for reviews.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:500',
        ]);
        
        $user = Auth::user();
        
        // Check if user has already reviewed this restaurant
        $existingReview = Review::where('user_id', $user->id)
            ->where('reviewable_id', $restaurant->id)
            ->where('reviewable_type', Restaurant::class)
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
                'reviewable_id' => $restaurant->id,
                'reviewable_type' => Restaurant::class,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
            
            $message = 'Review berhasil ditambahkan.';
        }
        
        return redirect()->back()->with('success', $message);
    }

    /**
     * Delete user's own review
     */
    public function deleteReview(Restaurant $restaurant)
    {
        $user = Auth::user();

        $review = Review::where('user_id', $user->id)
            ->where('reviewable_id', $restaurant->id)
            ->where('reviewable_type', Restaurant::class)
            ->first();

        if ($review) {
            $review->delete();
            return redirect()->back()->with('success', 'Review berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Review tidak ditemukan.');
    }
}