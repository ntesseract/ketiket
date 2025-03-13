<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RestaurantController extends Controller
{
    /**
     * Display a listing of restaurants
     */
    public function index(Request $request)
    {
        $query = Restaurant::query();
        
        // Apply filters
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%')
                  ->orWhere('cuisine_type', 'like', '%' . $request->search . '%');
            });
        }
        
        // Additional admin-specific filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Sort results
        $sortBy = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        
        $query->orderBy($sortBy, $sortOrder);
        
        // Get paginated results
        $restaurants = $query->paginate(20);
        
        // Get all cuisine types for filter
        $cuisineTypes = Restaurant::select('cuisine_type')
            ->distinct()
            ->whereNotNull('cuisine_type')
            ->pluck('cuisine_type');
        
        return view('admin.restaurants.index', compact('restaurants', 'cuisineTypes'));
    }

    /**
     * Show the form for creating a new restaurant
     */
    public function create()
    {
        return view('admin.restaurants.create');
    }

    /**
     * Store a newly created restaurant
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:restaurants,name',
            'description' => 'nullable|string',
            'cuisine_type' => 'nullable|string|max:100',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'contact_number' => 'nullable|string|max:20',
            'opening_hour' => 'nullable|date_format:H:i',
            'closing_hour' => 'nullable|date_format:H:i',
            'has_vegetarian_options' => 'boolean',
            'status' => ['nullable', Rule::in(['active', 'inactive', 'pending'])],
            'image' => 'nullable|image|max:2048', // 2MB max
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('restaurants', 'public');
            $validated['image_path'] = $imagePath;
        }

        // Create restaurant
        $restaurant = Restaurant::create($validated);

        return redirect()->route('admin.restaurants.index')
            ->with('success', 'Restaurant created successfully.');
    }

    /**
     * Display the specified restaurant
     */
    public function show(Restaurant $restaurant)
    {
        // Load related reviews and other details
        $restaurant->load('reviews.user');
        
        return view('admin.restaurants.show', compact('restaurant'));
    }

    /**
     * Show the form for editing the specified restaurant
     */
    public function edit(Restaurant $restaurant)
    {
        return view('admin.restaurants.edit', compact('restaurant'));
    }

    /**
     * Update the specified restaurant
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:restaurants,name,' . $restaurant->id,
            'description' => 'nullable|string',
            'cuisine_type' => 'nullable|string|max:100',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'contact_number' => 'nullable|string|max:20',
            'opening_hour' => 'nullable|date_format:H:i',
            'closing_hour' => 'nullable|date_format:H:i',
            'has_vegetarian_options' => 'boolean',
            'status' => ['nullable', Rule::in(['active', 'inactive', 'pending'])],
            'image' => 'nullable|image|max:2048', // 2MB max
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($restaurant->image_path) {
                Storage::disk('public')->delete($restaurant->image_path);
            }

            $imagePath = $request->file('image')->store('restaurants', 'public');
            $validated['image_path'] = $imagePath;
        }

        // Update restaurant
        $restaurant->update($validated);

        return redirect()->route('admin.restaurants.index')
            ->with('success', 'Restaurant updated successfully.');
    }

    /**
     * Remove the specified restaurant
     */
    public function destroy(Restaurant $restaurant)
    {
        // Delete associated image
        if ($restaurant->image_path) {
            Storage::disk('public')->delete($restaurant->image_path);
        }

        $restaurant->delete();

        return redirect()->route('admin.restaurants.index')
            ->with('success', 'Restaurant deleted successfully.');
    }

    /**
     * Manage restaurant reviews
     */
    public function manageReviews(Restaurant $restaurant)
    {
        $reviews = $restaurant->reviews()->with('user')->paginate(20);
        
        return view('admin.restaurants.reviews', compact('restaurant', 'reviews'));
    }

    /**
     * Delete a specific review
     */
    public function deleteReview(Restaurant $restaurant, $reviewId)
    {
        $review = $restaurant->reviews()->findOrFail($reviewId);
        $review->delete();

        return redirect()->back()
            ->with('success', 'Review deleted successfully.');
    }
}