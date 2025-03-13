<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\Review;
use App\Models\Booking;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class HotelController extends Controller
{
    /**
     * Display a listing of the hotels
     */
    public function index(Request $request)
    {
        $query = Hotel::query();
        
        // Apply filters
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
            });
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
        $sortBy = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        
        $query->orderBy($sortBy, $sortOrder);
        
        // Get paginated results
        $hotels = $query->withCount('bookings')->paginate(10);
        
        return view('admin.hotels.index', compact('hotels'));
    }
    
    /**
     * Show the form for creating a new hotel
     */
    public function create()
    {
        return view('admin.hotels.create');
    }
    
    /**
     * Store a newly created hotel in database
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:hotels',
            'description' => 'required|string|min:50',
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'price_per_night' => 'required|numeric|min:0',
            'star_rating' => 'required|integer|between:1,5',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Upload image
            $imagePath = $request->file('image')->store('hotels', 'public');
            
            // Create hotel
            $hotel = Hotel::create([
                'name' => $request->name,
                'description' => $request->description,
                'location' => $request->location,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'price_per_night' => $request->price_per_night,
                'star_rating' => $request->star_rating,
                'image' => $imagePath,
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.hotels.index')
                ->with('success', 'Hotel berhasil ditambahkan.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menambahkan hotel: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Display the specified hotel
     */
    public function show(Hotel $hotel)
    {
        // Get bookings for this hotel
        $bookings = Booking::where('hotel_id', $hotel->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        // Get reviews
        $reviews = $hotel->reviews()->with('user')->latest()->get();
        
        // Get occupancy data for chart
        $occupancyData = $this->getOccupancyData($hotel->id);
        
        return view('admin.hotels.show', compact('hotel', 'bookings', 'reviews', 'occupancyData'));
    }
    
    /**
     * Show the form for editing the specified hotel
     */
    public function edit(Hotel $hotel)
    {
        return view('admin.hotels.edit', compact('hotel'));
    }
    
    /**
     * Update the specified hotel in database
     */
    public function update(Request $request, Hotel $hotel)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('hotels')->ignore($hotel->id),
            ],
            'description' => 'required|string|min:50',
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'price_per_night' => 'required|numeric|min:0',
            'star_rating' => 'required|integer|between:1,5',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Update image if provided
            if ($request->hasFile('image')) {
                // Delete old image
                if ($hotel->image) {
                    Storage::disk('public')->delete($hotel->image);
                }
                
                // Upload new image
                $imagePath = $request->file('image')->store('hotels', 'public');
                $hotel->image = $imagePath;
            }
            
            // Update hotel
            $hotel->update([
                'name' => $request->name,
                'description' => $request->description,
                'location' => $request->location,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'price_per_night' => $request->price_per_night,
                'star_rating' => $request->star_rating,
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.hotels.index')
                ->with('success', 'Hotel berhasil diperbarui.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui hotel: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Remove the specified hotel from database
     */
    public function destroy(Hotel $hotel)
    {
        try {
            // Check if hotel has bookings
            $bookingsCount = Booking::where('hotel_id', $hotel->id)->count();
            
            if ($bookingsCount > 0) {
                return redirect()->back()
                    ->with('error', 'Hotel tidak dapat dihapus karena memiliki ' . $bookingsCount . ' booking terkait.');
            }
            
            DB::beginTransaction();
            
            // Delete image
            if ($hotel->image) {
                Storage::disk('public')->delete($hotel->image);
            }
            
            // Delete reviews
            Review::where('reviewable_id', $hotel->id)
                ->where('reviewable_type', Hotel::class)
                ->delete();
            
            // Delete hotel
            $hotel->delete();
            
            DB::commit();
            
            return redirect()->route('admin.hotels.index')
                ->with('success', 'Hotel berhasil dihapus.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus hotel: ' . $e->getMessage());
        }
    }
    
    /**
     * Get occupancy data for specified hotel
     */
    private function getOccupancyData($hotelId)
    {
        // Get bookings for the last 6 months grouped by month
        $sixMonthsAgo = now()->subMonths(6)->startOfMonth();
        
        $bookings = Booking::where('hotel_id', $hotelId)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total_bookings'),
                DB::raw('SUM(number_of_tickets) as total_rooms')
            )
            ->groupBy('year', 'month')
            ->get();
            
        // Format data for chart
        $chartData = [];
        
        $currentDate = $sixMonthsAgo->copy();
        $endDate = now()->endOfMonth();
        
        while ($currentDate <= $endDate) {
            $year = $currentDate->year;
            $month = $currentDate->month;
            
            $monthData = $bookings->first(function ($item) use ($year, $month) {
                return $item->year == $year && $item->month == $month;
            });
            
            $chartData[] = [
                'month' => $currentDate->format('M Y'), // Jan 2023, Feb 2023, etc.
                'bookings' => $monthData ? $monthData->total_bookings : 0,
                'rooms' => $monthData ? $monthData->total_rooms : 0,
            ];
            
            $currentDate->addMonth();
        }
        
        return $chartData;
    }
    
    /**
     * Manage hotel reviews
     */
    public function reviews(Hotel $hotel)
    {
        $reviews = $hotel->reviews()->with('user')->latest()->paginate(10);
        
        return view('admin.hotels.reviews', compact('hotel', 'reviews'));
    }
    
    /**
     * Delete a review
     */
    public function deleteReview($id)
    {
        try {
            $review = Review::findOrFail($id);
            $review->delete();
            
            return redirect()->back()
                ->with('success', 'Review berhasil dihapus.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus review: ' . $e->getMessage());
        }
    }
}