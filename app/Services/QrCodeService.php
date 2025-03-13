<?php
// app/Services/QrCodeService.php

namespace App\Services;

use App\Models\Booking;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class QrCodeService
{
    /**
     * Generate QR code for a booking
     */
    public function generateQrCode(Booking $booking)
    {
        // Prepare data to encode in QR
        $qrCodeData = [
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'destination' => $booking->destination ? $booking->destination->name : null,
            'hotel' => $booking->hotel ? $booking->hotel->name : null,
            'restaurant' => $booking->restaurant ? $booking->restaurant->name : null,
            'visit_date' => $booking->visit_date->format('Y-m-d H:i'),
            'tickets' => $booking->number_of_tickets,
            'status' => $booking->status,
            'is_package' => $booking->is_package,
            'timestamp' => now()->timestamp,
        ];

        // Generate QR code
        $qrCodeImage = QrCode::format('png')
            ->size(300)
            ->errorCorrection('H')
            ->margin(1)
            ->generate(json_encode($qrCodeData));

        // Save QR code to storage
        $filename = 'qrcodes/booking-' . $booking->id . '.png';
        Storage::disk('public')->put($filename, $qrCodeImage);

        // Update booking with QR code path
        $booking->qr_code = $filename;
        $booking->save();

        return $filename;
    }
    
    /**
     * Verify QR code data
     */
    public function verifyQrCode($qrData)
    {
        try {
            // Decode QR data
            $data = json_decode($qrData, true);
            
            if (!isset($data['booking_id'])) {
                return [
                    'valid' => false,
                    'message' => 'QR Code tidak valid: Data tidak lengkap'
                ];
            }
            
            // Get booking from database
            $booking = Booking::find($data['booking_id']);
            
            if (!$booking) {
                return [
                    'valid' => false,
                    'message' => 'Booking tidak ditemukan'
                ];
            }
            
            // Check booking status
            if ($booking->status !== 'confirmed') {
                return [
                    'valid' => false,
                    'message' => 'Booking tidak valid: Status ' . $booking->status
                ];
            }
            
            // Verify visit date (if it's today)
            $bookingDate = $booking->visit_date->format('Y-m-d');
            $today = now()->format('Y-m-d');
            
            if ($bookingDate != $today) {
                return [
                    'valid' => false,
                    'message' => 'Tiket hanya valid untuk tanggal ' . $booking->visit_date->format('d M Y')
                ];
            }
            
            // Everything is valid, mark booking as completed
            $booking->status = 'completed';
            $booking->save();
            
            return [
                'valid' => true,
                'message' => 'QR Code valid. Pengunjung dapat masuk.',
                'booking' => $booking
            ];
            
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Check if a booking has an existing QR code
     */
    public function hasQrCode(Booking $booking)
    {
        return $booking->qr_code && Storage::disk('public')->exists($booking->qr_code);
    }
    
    /**
     * Delete QR code for a booking
     */
    public function deleteQrCode(Booking $booking)
    {
        if ($this->hasQrCode($booking)) {
            Storage::disk('public')->delete($booking->qr_code);
            $booking->qr_code = null;
            $booking->save();
            return true;
        }
        
        return false;
    }
    
    /**
     * Regenerate QR code for a booking
     */
    public function regenerateQrCode(Booking $booking)
    {
        // Delete existing QR code first
        $this->deleteQrCode($booking);
        
        // Generate new QR code
        return $this->generateQrCode($booking);
    }
}