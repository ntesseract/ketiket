<?php
// app/Http/Controllers/Admin/PackageController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TravelPackage;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    /**
     * Display a listing of travel packages
     */
    public function index(Request $request)
    {
        $query = TravelPackage::withCount(['destinations', 'hotels', 'restaurants']);
        
        // Apply filters
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }
        
        if ($request->has('duration')) {
            $query->where('duration_days', $request->duration);
        }
        
        // Sort
        $sort = $request->sort ?? 'name';
        $direction = $request->direction ?? 'asc';
        
        $query->orderBy($sort, $direction);
        
        $packages = $query->paginate(10);
        
        return view('admin.packages.index', compact('packages'));
    }

    /**
     * Show the form for creating a new package
     */
    public function create()
    {
        return view('admin.packages.create');
    }

    /**
     * Store a newly created package
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'image' => 'nullable|image|max:5120', // max 5MB
        ]);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('packages', 'public');
            $validated['image'] = $imagePath;
        }
        
        $package = TravelPackage::create($validated);
        
        return redirect()->route('admin.packages.attractions', $package->id)
            ->with('success', 'Paket wisata berhasil dibuat. Silakan tambahkan destinasi, hotel, dan restoran ke paket ini.');
    }

    /**
     * Display the specified package
     */
    public function show(TravelPackage $package)
    {
        $package->load(['destinations', 'hotels', 'restaurants']);
        
        return view('admin.packages.show', compact('package'));
    }

    /**
     * Show the form for editing the package
     */
    public function edit(TravelPackage $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    /**
     * Update the package
     */
    public function update(Request $request, TravelPackage $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'image' => 'nullable|image|max:5120', // max 5MB
        ]);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($package->image) {
                Storage::disk('public')->delete($package->image);
            }
            
            $imagePath = $request->file('image')->store('packages', 'public');
            $validated['image'] = $imagePath;
        }
        
        $package->update($validated);
        
        return redirect()->route('admin.packages.index')
            ->with('success', 'Paket wisata berhasil diperbarui.');
    }

    /**
     * Remove the package
     */
    public function destroy(TravelPackage $package)
    {
        // Delete image if exists
        if ($package->image) {
            Storage::disk('public')->delete($package->image);
        }
        
        // Detach all relations
        $package->destinations()->detach();
        $package->hotels()->detach();
        $package->restaurants()->detach();
        
        $package->delete();
        
        return redirect()->route('admin.packages.index')
            ->with('success', 'Paket wisata berhasil dihapus.');
    }
    
    /**
     * Show form to manage package attractions
     */
    public function attractions(TravelPackage $package)
    {
        $package->load(['destinations', 'hotels', 'restaurants']);
        
        // Get all possible destinations, hotels, and restaurants
        $destinations = Destination::orderBy('name')->get();
        $hotels = Hotel::orderBy('name')->get();
        $restaurants = Restaurant::orderBy('name')->get();
        
        return view('admin.packages.attractions', compact('package', 'destinations', 'hotels', 'restaurants'));
    }
    
    /**
     * Add a destination to package
     */
    public function addDestination(Request $request, TravelPackage $package)
    {
        $request->validate([
            'destination_id' => 'required|exists:destinations,id',
        ]);
        
        // Check if destination is already in package
        if ($package->destinations()->where('destination_id', $request->destination_id)->exists()) {
            return redirect()->back()
                ->with('error', 'Destinasi ini sudah ada dalam paket.');
        }
        
        $package->destinations()->attach($request->destination_id);
        
        return redirect()->back()
            ->with('success', 'Destinasi berhasil ditambahkan ke paket.');
    }
    
    /**
     * Add a hotel to package
     */
    public function addHotel(Request $request, TravelPackage $package)
    {
        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
        ]);
        
        // Check if hotel is already in package
        if ($package->hotels()->where('hotel_id', $request->hotel_id)->exists()) {
            return redirect()->back()
                ->with('error', 'Hotel ini sudah ada dalam paket.');
        }
        
        $package->hotels()->attach($request->hotel_id);
        
        return redirect()->back()
            ->with('success', 'Hotel berhasil ditambahkan ke paket.');
    }
    
    /**
     * Add a restaurant to package
     */
    public function addRestaurant(Request $request, TravelPackage $package)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
        ]);
        
        // Check if restaurant is already in package
        if ($package->restaurants()->where('restaurant_id', $request->restaurant_id)->exists()) {
            return redirect()->back()
                ->with('error', 'Restoran ini sudah ada dalam paket.');
        }
        
        $package->restaurants()->attach($request->restaurant_id);
        
        return redirect()->back()
            ->with('success', 'Restoran berhasil ditambahkan ke paket.');
    }
    
    /**
     * Remove a destination from package
     */
    public function removeDestination(TravelPackage $package, Destination $destination)
    {
        $package->destinations()->detach($destination->id);
        
        return redirect()->back()
            ->with('success', 'Destinasi berhasil dihapus dari paket.');
    }
    
    /**
     * Remove a hotel from package
     */
    public function removeHotel(TravelPackage $package, Hotel $hotel)
    {
        $package->hotels()->detach($hotel->id);
        
        return redirect()->back()
            ->with('success', 'Hotel berhasil dihapus dari paket.');
    }
    
    /**
     * Remove a restaurant from package
     */
    public function removeRestaurant(TravelPackage $package, Restaurant $restaurant)
    {
        $package->restaurants()->detach($restaurant->id);
        
        return redirect()->back()
            ->with('success', 'Restoran berhasil dihapus dari paket.');
    }
}