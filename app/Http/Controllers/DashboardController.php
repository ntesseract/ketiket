<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\TravelPackage;
use App\Models\WalletTransaction;
use App\Models\UserNotification;
use App\Services\RecommendationService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $recommendationService;
    
    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }
    
    /**
     * Display user dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get wallet balance
        $balance = $user->balance;
        
        // Get recent bookings
        $recentBookings = Booking::where('user_id', $user->id)
            ->with(['destination', 'hotel', 'restaurant'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get upcoming bookings
        $upcomingBookings = Booking::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->where('visit_date', '>=', now())
            ->with(['destination', 'hotel', 'restaurant'])
            ->orderBy('visit_date')
            ->limit(3)
            ->get();
        
        // Get favorited destinations
        $favoriteIds = DB::table('favorites')
        ->where('user_id', $user->id)
        ->pluck('destination_id');
        
        $favorites = Destination::whereIn('id', $favoriteIds)
        ->with('reviews')
        ->limit(4)
        ->get();
        // Get unread notifications
        $unreadNotifications = UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get personalized recommendations
        $recommendedDestinations = $this->recommendationService->getDestinationRecommendations($user, 3);
        
        // Get featured packages
        $featuredPackages = TravelPackage::withCount(['destinations', 'hotels', 'restaurants'])
            ->having('destinations_count', '>', 0)
            ->orderBy('price')
            ->limit(2)
            ->get();
        
        // Get booking statistics
        $bookingStats = [
            'total' => Booking::where('user_id', $user->id)->count(),
            'completed' => Booking::where('user_id', $user->id)->where('status', 'completed')->count(),
            'upcoming' => $upcomingBookings->count(),
        ];
        
        // Get wallet transaction statistics
        $walletStats = [
            'balance' => $balance,
            'totalSpent' => WalletTransaction::where('user_id', $user->id)
                ->where('type', 'payment')
                ->sum('amount'),
        ];
        
        return view('dashboard', compact(
            'balance',
            'recentBookings',
            'upcomingBookings',
            'favorites',
            'unreadNotifications',
            'recommendedDestinations',
            'featuredPackages',
            'bookingStats',
            'walletStats'
        ));
    }
}