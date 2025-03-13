<?php
// app/Http/Controllers/Admin/DestinationController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\LocationService;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DestinationController extends Controller
{
    protected $locationService;
    
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Destination::query();
        
        // Apply filters
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('location', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }
        
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        // Sort
        $sort = $request->sort ?? 'name';
        $direction = $request->direction ?? 'asc';
        
        $query->orderBy($sort, $direction);
        
        $destinations = $query->paginate(10);
        
        return view('admin.destinations.index', compact('destinations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.destinations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:5120', // max 5MB
            'capacity' => 'nullable|integer|min:0',
            'opening_hour' => 'nullable|date_format:H:i',
            'closing_hour' => 'nullable|date_format:H:i',
            'status' => ['required', Rule::in(['open', 'closed', 'maintenance'])],
        ]);
        
        // Check if coordinates are provided
        if (!$request->filled('latitude') || !$request->filled('longitude')) {
            // Try to get coordinates from location name
            $locationData = $this->locationService->searchLocation($request->location);
            
            if (!empty($locationData) && isset($locationData[0])) {
                $validated['latitude'] = $locationData[0]['lat'];
                $validated['longitude'] = $locationData[0]['lon'];
            }
        }
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('destinations', 'public');
            $validated['image'] = $imagePath;
        }
        
        Destination::create($validated);
        
        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destinasi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Destination $destination)
    {
        $destination->load(['bookings.user', 'reviews.user']);
        
        // Get stats
        $totalBookings = $destination->bookings->count();
        $totalVisitors = $destination->bookings->sum('number_of_tickets');
        $averageRating = $destination->averageRating();
        $totalReviews = $destination->reviews->count();
        
        // Get map URL if coordinates are available
        $mapUrl = null;
        if ($destination->latitude && $destination->longitude) {
            $mapUrl = $this->locationService->getStaticMapUrl(
                $destination->latitude,
                $destination->longitude
            );
        }
        
        return view('admin.destinations.show', compact(
            'destination',
            'totalBookings',
            'totalVisitors',
            'averageRating',
            'totalReviews',
            'mapUrl'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Destination $destination)
    {
        return view('admin.destinations.edit', compact('destination'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Destination $destination)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:5120', // max 5MB
            'capacity' => 'nullable|integer|min:0',
            'opening_hour' => 'nullable|date_format:H:i',
            'closing_hour' => 'nullable|date_format:H:i',
            'status' => ['required', Rule::in(['open', 'closed', 'maintenance'])],
        ]);
        
        // Check if coordinates are provided
        if (!$request->filled('latitude') || !$request->filled('longitude')) {
            // Try to get coordinates from location name
            $locationData = $this->locationService->searchLocation($request->location);
            
            if (!empty($locationData) && isset($locationData[0])) {
                $validated['latitude'] = $locationData[0]['lat'];
                $validated['longitude'] = $locationData[0]['lon'];
            }
        }
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($destination->image) {
                Storage::disk('public')->delete($destination->image);
            }
            
            $imagePath = $request->file('image')->store('destinations', 'public');
            $validated['image'] = $imagePath;
        }
        
        $destination->update($validated);
        
        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destinasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Destination $destination)
    {
        // Check if destination has bookings
        if ($destination->bookings()->exists()) {
            return redirect()->route('admin.destinations.index')
                ->with('error', 'Tidak dapat menghapus destinasi karena masih memiliki booking.');
        }
        
        // Delete image if exists
        if ($destination->image) {
            Storage::disk('public')->delete($destination->image);
        }
        
        $destination->delete();
        
        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destinasi berhasil dihapus.');
    }
}