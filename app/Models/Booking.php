<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'destination_id',
        'hotel_id',
        'restaurant_id',
        'visit_date',
        'number_of_tickets',
        'total_price',
        'qr_code',
        'status',
        'is_package',
    ];

    protected $casts = [
        'visit_date' => 'datetime',
        'is_package' => 'boolean',
    ];

    /**
     * Get the user who made this booking
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the destination associated with this booking
     */
    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    /**
     * Get the hotel associated with this booking
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get the restaurant associated with this booking
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Generate QR code for this booking
     */
    public function generateQRCode()
    {
        $qrCodeData = [
            'booking_id' => $this->id,
            'user_id' => $this->user_id,
            'destination' => $this->destination ? $this->destination->name : null,
            'visit_date' => $this->visit_date->format('Y-m-d H:i'),
            'tickets' => $this->number_of_tickets,
            'status' => $this->status,
        ];

        $qrCodeImage = QrCode::format('png')
            ->size(300)
            ->errorCorrection('H')
            ->generate(json_encode($qrCodeData));

        $filename = 'qrcodes/booking-' . $this->id . '.png';
        Storage::disk('public')->put($filename, $qrCodeImage);

        $this->qr_code = $filename;
        $this->save();

        return $filename;
    }

    /**
     * Check if booking is for a package
     */
    public function isPackage()
    {
        return $this->is_package && 
               ($this->destination_id !== null && 
                $this->hotel_id !== null && 
                $this->restaurant_id !== null);
    }
}