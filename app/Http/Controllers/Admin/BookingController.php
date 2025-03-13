<?php
// app/Http/Controllers/Admin/BookingController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use App\Services\QrCodeService;
use App\Services\NotificationService;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    protected $qrCodeService;
    protected $notificationService;
    
    public function __construct(QrCodeService $qrCodeService, NotificationService $notificationService)
    {
        $this->qrCodeService = $qrCodeService;
        $this->notificationService = $notificationService;
    }
    
    /**
     * Display a listing of all bookings (admin dashboard)
     */
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'destination', 'hotel', 'restaurant']);
        
        // Apply filters
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            })
            ->orWhereHas('destination', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }
        
        if ($request->has('date_from')) {
            $query->whereDate('visit_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->whereDate('visit_date', '<=', $request->date_to);
        }
        
        // Sort
        $sort = $request->sort ?? 'created_at';
        $direction = $request->direction ?? 'desc';
        
        $query->orderBy($sort, $direction);
        
        $bookings = $query->paginate(20);
        
        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Display the specified booking (admin view)
     */
    public function show(Booking $booking)
    {
        $booking->load(['user', 'destination', 'hotel', 'restaurant']);
        
        // Get QR code data if exists
        $qrCodeUrl = null;
        if ($booking->qr_code) {
            $qrCodeUrl = asset('storage/' . $booking->qr_code);
        }
        
        return view('admin.bookings.show', compact('booking', 'qrCodeUrl'));
    }

    /**
     * Update booking status (admin only)
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'confirmed', 'cancelled', 'completed'])],
            'notification_message' => 'nullable|string',
        ]);
        
        $oldStatus = $booking->status;
        $newStatus = $validated['status'];
        
        // Update booking status
        $booking->status = $newStatus;
        $booking->save();
        
        // Generate QR code if status changed to confirmed
        if ($newStatus === 'confirmed' && ($oldStatus !== 'confirmed' || !$booking->qr_code)) {
            $this->qrCodeService->generateQrCode($booking);
        }
        
        // Send notification to user
        $message = $validated['notification_message'] ?? $this->getDefaultStatusMessage($newStatus, $booking);
        
        // Create notification in database
        UserNotification::create([
            'user_id' => $booking->user_id,
            'title' => 'Status Booking Diperbarui',
            'message' => $message,
            'type' => 'booking',
            'is_read' => false,
        ]);
        
        // Try to send notification through service if method exists
        try {
            if (method_exists($this->notificationService, 'sendNotification') && $booking->user) {
                // Use the generic sendNotification method
                $this->notificationService->sendNotification(
                    $booking->user,
                    'Status Booking Diperbarui',
                    $message,
                    'booking'
                );
            }
        } catch (\Exception $e) {
            // Log error but continue
            \Illuminate\Support\Facades\Log::error('Failed to send notification: ' . $e->getMessage());
        }
        
        return redirect()->back()->with('success', 'Status booking berhasil diperbarui.');
    }
    
    /**
     * Get default status update message
     */
    private function getDefaultStatusMessage($status, $booking)
    {
        $destinationName = $booking->destination ? $booking->destination->name : 'Paket Wisata';
        
        switch ($status) {
            case 'confirmed':
                return "Booking Anda untuk {$destinationName} pada tanggal {$booking->visit_date->format('d M Y')} telah dikonfirmasi. Silakan cek QR Code tiket digital Anda.";
            case 'cancelled':
                return "Booking Anda untuk {$destinationName} pada tanggal {$booking->visit_date->format('d M Y')} telah dibatalkan.";
            case 'completed':
                return "Booking Anda untuk {$destinationName} pada tanggal {$booking->visit_date->format('d M Y')} telah selesai. Terima kasih telah menggunakan layanan kami.";
            default:
                return "Status booking Anda untuk {$destinationName} pada tanggal {$booking->visit_date->format('d M Y')} telah diperbarui menjadi {$status}.";
        }
    }
    
    /**
     * Generate QR code for booking (admin only)
     */
    public function generateQrCode(Booking $booking)
    {
        if ($booking->status !== 'confirmed') {
            return redirect()->back()->with('error', 'QR Code hanya dapat dibuat untuk booking yang sudah dikonfirmasi.');
        }
        
        try {
            $filename = $this->qrCodeService->regenerateQrCode($booking);
            return redirect()->back()->with('success', 'QR Code berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat QR Code: ' . $e->getMessage());
        }
    }
    
    /**
     * Download booking data as CSV (admin only)
     */
    public function exportCsv(Request $request)
    {
        $query = Booking::with(['user', 'destination', 'hotel', 'restaurant']);
        
        // Apply filters (same as index method)
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('date_from')) {
            $query->whereDate('visit_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->whereDate('visit_date', '<=', $request->date_to);
        }
        
        $bookings = $query->get();
        
        // Create CSV content
        $headers = [
            'ID',
            'User',
            'Email',
            'Destination',
            'Hotel',
            'Restaurant',
            'Visit Date',
            'Tickets',
            'Total Price',
            'Status',
            'Created At'
        ];
        
        $callback = function() use ($bookings, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            
            foreach ($bookings as $booking) {
                $row = [
                    $booking->id,
                    $booking->user->name ?? 'N/A',
                    $booking->user->email ?? 'N/A',
                    $booking->destination->name ?? 'N/A',
                    $booking->hotel->name ?? 'N/A',
                    $booking->restaurant->name ?? 'N/A',
                    $booking->visit_date->format('Y-m-d H:i'),
                    $booking->number_of_tickets,
                    number_format($booking->total_price, 0, ',', '.'),
                    $booking->status,
                    $booking->created_at->format('Y-m-d H:i')
                ];
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        $filename = 'bookings_' . date('Y-m-d') . '.csv';
        
        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    /**
     * Display all reviews (admin only)
     */
    public function reviews()
    {
        $reviews = Review::with(['user', 'reviewable'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.reviews.index', compact('reviews'));
    }
    
    /**
     * Delete review (admin only)
     */
    public function deleteReview(Review $review)
    {
        $review->delete();
        
        return redirect()->back()->with('success', 'Review berhasil dihapus.');
    }
}