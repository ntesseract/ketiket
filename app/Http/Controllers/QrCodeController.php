<?php
// app/Http/Controllers/QrCodeController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    /**
     * Generate QR code for a booking
     */
    public function generate($bookingId)
    {
        $user = Auth::user();
        $booking = Booking::findOrFail($bookingId);
        
        // Check if booking belongs to user or user is admin
        if ($booking->user_id !== $user->id && $user->role !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke booking ini.');
        }
        
        // Check if booking is confirmed
        if ($booking->status !== 'confirmed') {
            return redirect()->back()->with('error', 'QR Code hanya dapat dibuat untuk booking yang sudah dikonfirmasi.');
        }
        
        // Generate QR code if not already generated
        if (!$booking->qr_code) {
            $booking->generateQRCode();
        }
        
        return view('booking.qrcode', compact('booking'));
    }
    
    /**
     * Download QR code
     */
    public function download($bookingId)
    {
        $user = Auth::user();
        $booking = Booking::findOrFail($bookingId);
        
        // Periksa apakah pengguna memiliki akses
        if ($booking->user_id !== $user->id && $user->role !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke booking ini.');
        }
        
        // Periksa apakah QR Code ada
        if (!$booking->qr_code) {
            return redirect()->back()->with('error', 'QR Code tidak ditemukan. Silakan generate ulang.');
        }
    
        // Path lengkap file
        $filePath = storage_path('app/public/' . $booking->qr_code);
        
        // Periksa keberadaan file
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File QR Code tidak ditemukan di sistem.');
        }
    
        // Download file
        return response()->download(
            $filePath, 
            'tiket-' . $booking->id . '.png', 
            [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'attachment; filename="tiket-' . $booking->id . '.png"'
            ]
        );
    }
    
    /**
     * Verify QR code (for admin/staff use)
     */
    public function verifyForm()
    {
        // Only admin can access this
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        return view('admin.qrcode.verify');
    }
    
    /**
     * Process QR code verification
     */
    public function verify(Request $request)
    {
        // Only admin can access this
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur ini.');
        }
        
        $request->validate([
            'qr_data' => 'required',
        ]);
        
        try {
            // Decode QR data
            $data = json_decode($request->qr_data, true);
            
            if (!isset($data['booking_id'])) {
                return redirect()->back()->with('error', 'QR Code tidak valid.');
            }
            
            $booking = Booking::findOrFail($data['booking_id']);
            
            // Check if booking is valid
            if ($booking->status !== 'confirmed') {
                return redirect()->back()->with('error', 'Booking tidak valid atau sudah digunakan.');
            }
            
            // Update booking status to completed
            $booking->status = 'completed';
            $booking->save();
            
            // Send notification to user
            \App\Models\UserNotification::create([
                'user_id' => $booking->user_id,
                'title' => 'Tiket Telah Digunakan',
                'message' => "Tiket Anda untuk " . ($booking->destination ? $booking->destination->name : 'Paket Wisata') . " telah berhasil digunakan. Selamat menikmati perjalanan Anda!",
                'type' => 'booking',
                'is_read' => false,
            ]);
            
            return redirect()->back()->with('success', 'Verifikasi berhasil. Pengunjung dapat masuk.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memverifikasi QR Code. ' . $e->getMessage());
        }
    }
    
    /**
     * Scanner page for admin
     */
    public function scanner()
    {
        // Only admin can access this
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        return view('admin.qrcode.scanner');
    }
}