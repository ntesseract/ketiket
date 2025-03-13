<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function index()
    {
        // Get counts
        $userCount = User::where('role', 'user')->count();
        $destinationCount = Destination::count();
        $bookingCount = Booking::count();
        $revenue = Booking::where('status', 'completed')->sum('total_price');
        
        // Get recent bookings
        $recentBookings = Booking::with(['user', 'destination'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get top destinations
        $topDestinations = Destination::withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->limit(5)
            ->get();
        
        // Get booking statistics by month
        $bookingStats = Booking::select(
                DB::raw('MONTH(created_at) as month'), 
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed'),
                DB::raw('SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Format for chart
        $months = [];
        $bookingData = [];
        $completedData = [];
        $cancelledData = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('M', mktime(0, 0, 0, $i, 1));
            $months[] = $monthName;
            
            $found = false;
            foreach ($bookingStats as $stat) {
                if ($stat->month == $i) {
                    $bookingData[] = $stat->total;
                    $completedData[] = $stat->completed;
                    $cancelledData[] = $stat->cancelled;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $bookingData[] = 0;
                $completedData[] = 0;
                $cancelledData[] = 0;
            }
        }
        
        return view('admin.dashboard.index', compact(
            'userCount', 
            'destinationCount', 
            'bookingCount', 
            'revenue', 
            'recentBookings', 
            'topDestinations',
            'months',
            'bookingData',
            'completedData',
            'cancelledData'
        ));
    }
}