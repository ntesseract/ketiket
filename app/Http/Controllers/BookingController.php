<?php
// app/Http/Controllers/BookingController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\TravelPackage;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;
use App\Services\LocationService;

class BookingController extends Controller
{
    protected $locationService;
    
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }
    
    /**
     * Display a listing of the bookings for the authenticated user
     */
    public function index()
    {
        $user = Auth::user();
        $bookings = Booking::where('user_id', $user->id)
            ->with(['destination', 'hotel', 'restaurant'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('booking.index', compact('bookings'));
    }
    
    /**
     * Display booking history for the authenticated user
     */
    public function history()
    {
        $user = Auth::user();
        $bookings = Booking::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with(['destination', 'hotel', 'restaurant'])
            ->orderBy('visit_date', 'desc')
            ->paginate(10);
            
        return view('booking.history', compact('bookings'));
    }
    
    /**
     * Show the form for creating a new booking for a destination
     */
    public function createDestination(Destination $destination)
    {
        return view('booking.create_destination', compact('destination'));
    }
    
    /**
     * Store a newly created booking for a destination
     */
    public function storeDestination(Request $request, Destination $destination)
    {
        $request->validate([
            'visit_date' => 'required|date|after:today',
            'number_of_tickets' => 'required|integer|min:1',
        ]);
        
        $user = Auth::user();
        $totalPrice = $destination->price * $request->number_of_tickets;
        
        // Create booking
        $booking = Booking::create([
            'user_id' => $user->id,
            'destination_id' => $destination->id,
            'visit_date' => $request->visit_date,
            'number_of_tickets' => $request->number_of_tickets,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'is_package' => false,
        ]);
        
        // Create notification
        UserNotification::create([
            'user_id' => $user->id,
            'title' => 'Booking Baru',
            'message' => "Booking Anda untuk {$destination->name} telah dibuat. Silakan lakukan pembayaran.",
            'type' => 'booking',
            'is_read' => false,
        ]);
        
        return redirect()->route('booking.payment', $booking->id)
            ->with('success', 'Booking berhasil dibuat. Silakan lanjutkan ke pembayaran.');
    }
    
    /**
     * Show the form for creating a new booking for a package
     */
    public function createPackage(TravelPackage $package)
    {
        return view('booking.create_package', compact('package'));
    }
    
    /**
     * Store a newly created booking for a package
     */
    public function storePackage(Request $request, TravelPackage $package)
    {
        $request->validate([
            'visit_date' => 'required|date|after:today',
            'number_of_tickets' => 'required|integer|min:1',
        ]);
        
        $user = Auth::user();
        
        try {
            // Create booking through package method
            $booking = $package->createBooking(
                $user->id,
                $request->visit_date,
                $request->number_of_tickets
            );
            
            // Create notification
            UserNotification::create([
                'user_id' => $user->id,
                'title' => 'Booking Paket Wisata',
                'message' => "Booking paket wisata '{$package->name}' telah dibuat. Silakan lakukan pembayaran.",
                'type' => 'booking',
                'is_read' => false,
            ]);
            
            return redirect()->route('booking.payment', $booking->id)
                ->with('success', 'Booking paket wisata berhasil dibuat. Silakan lanjutkan ke pembayaran.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat membuat booking: ' . $e->getMessage());
        }
    }
    
    /**
     * Display the specified booking
     */
    public function show(Booking $booking)
    {
        $user = Auth::user();
        
        // Check if booking belongs to user or user is admin
        if ($booking->user_id !== $user->id && $user->role !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke booking ini.');
        }
        
        // Get location data for map
        $mapUrl = null;
        if ($booking->destination && $booking->destination->latitude && $booking->destination->longitude) {
            $mapUrl = $this->locationService->getStaticMapUrl(
                $booking->destination->latitude,
                $booking->destination->longitude
            );
        }
        
        return view('booking.show', compact('booking', 'mapUrl'));
    }
    
    /**
     * Show payment page for a booking
     */
    public function payment(Booking $booking)
    {
        $user = Auth::user();
        
        // Check if booking belongs to user
        if ($booking->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke booking ini.');
        }
        
        // Check if booking is pending
        if ($booking->status !== 'pending') {
            return redirect()->route('booking.show', $booking->id)
                ->with('error', 'Booking ini sudah dibayar atau dibatalkan.');
        }
        
        $balance = $user->balance;
        $sufficientBalance = $balance >= $booking->total_price;
        
        return view('booking.payment', compact('booking', 'balance', 'sufficientBalance'));
    }
    
    /**
     * Cancel booking
     */
    public function cancel(Booking $booking)
    {
        $user = Auth::user();
        
        // Check if booking belongs to user
        if ($booking->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke booking ini.');
        }
        
        // Check if booking can be cancelled
        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya booking dengan status pending yang dapat dibatalkan.');
        }
        
        // Update booking status
        $booking->status = 'cancelled';
        $booking->save();
        
        // Create notification
        UserNotification::create([
            'user_id' => $user->id,
            'title' => 'Booking Dibatalkan',
            'message' => "Booking Anda untuk " . ($booking->destination ? $booking->destination->name : 'paket wisata') . " telah dibatalkan.",
            'type' => 'booking',
            'is_read' => false,
        ]);
        
        return redirect()->route('booking.index')
            ->with('success', 'Booking berhasil dibatalkan.');
    }
}