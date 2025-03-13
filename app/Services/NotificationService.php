<?php
// app/Services/NotificationService.php

namespace App\Services;

use App\Models\UserNotification;
use App\Models\User;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Events\NewNotification;
use Illuminate\Support\Facades\Mail;
// use App\Mail\NotificationMail; // Uncomment ini jika Anda sudah membuat mail class

class NotificationService
{
    /**
     * Send a notification to a user
     */
    public function sendNotification(User $user, string $title, string $message, string $type = 'general')
    {
        // Create notification in database
        $notification = UserNotification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'is_read' => false,
        ]);
        
        // Broadcast event for real-time updates
        broadcast(new NewNotification($notification))->toOthers();
        
        // Send email notification if user has email
        // Uncomment line below if you have already set up email
        /*
        if ($user->email) {
            Mail::to($user->email)->queue(new NotificationMail($notification));
        }
        */
        
        return $notification;
    }
    
    /**
     * Send booking confirmation notification
     */
    public function sendBookingConfirmation(Booking $booking)
    {
        $user = $booking->user;
        $destinationName = $booking->destination ? $booking->destination->name : 'Paket Wisata';
        
        return $this->sendNotification(
            $user,
            'Booking Dikonfirmasi',
            "Booking Anda untuk {$destinationName} pada tanggal {$booking->visit_date->format('d M Y')} telah dikonfirmasi. Silakan lihat QR Code tiket digital Anda.",
            'booking'
        );
    }
    
    /**
     * Send booking cancellation notification
     */
    public function sendBookingCancellation(Booking $booking)
    {
        $user = $booking->user;
        $destinationName = $booking->destination ? $booking->destination->name : 'Paket Wisata';
        
        return $this->sendNotification(
            $user,
            'Booking Dibatalkan',
            "Booking Anda untuk {$destinationName} pada tanggal {$booking->visit_date->format('d M Y')} telah dibatalkan.",
            'booking'
        );
    }
    
    /**
     * Send booking status update notification
     * 
     * @param Booking $booking
     * @param string $message
     * @return UserNotification
     */
    public function sendBookingStatusUpdate(Booking $booking, string $message)
    {
        $user = $booking->user;
        
        return $this->sendNotification(
            $user,
            'Status Booking Diperbarui',
            $message,
            'booking'
        );
    }
    
    /**
     * Send wallet transaction notification
     */
    public function sendWalletTransactionNotification(User $user, string $type, float $amount, string $description)
    {
        $formattedAmount = number_format($amount, 0, ',', '.');
        
        $title = '';
        $message = '';
        
        switch ($type) {
            case 'top_up':
                $title = 'Top Up Berhasil';
                $message = "Saldo sebesar Rp {$formattedAmount} telah berhasil ditambahkan ke akun Anda.";
                break;
            case 'payment':
                $title = 'Pembayaran Berhasil';
                $message = "Pembayaran sebesar Rp {$formattedAmount} telah berhasil. {$description}";
                break;
            case 'refund':
                $title = 'Refund Berhasil';
                $message = "Refund sebesar Rp {$formattedAmount} telah dikembalikan ke saldo Anda. {$description}";
                break;
            default:
                $title = 'Transaksi Wallet';
                $message = "Transaksi sebesar Rp {$formattedAmount} telah diproses. {$description}";
        }
        
        return $this->sendNotification($user, $title, $message, 'wallet');
    }
    
    /**
     * Send promo notification to all users
     */
    public function sendPromoNotification(string $title, string $message, $entityType = null, $entityId = null)
    {
        $users = User::where('role', 'user')->get();
        $notifications = [];
        
        foreach ($users as $user) {
            $notification = $this->sendNotification($user, $title, $message, 'promo');
            $notifications[] = $notification;
        }
        
        return $notifications;
    }
    
    /**
     * Send destination promo notification
     */
    public function sendDestinationPromo(Destination $destination, string $promoTitle, string $promoMessage)
    {
        return $this->sendPromoNotification(
            $promoTitle,
            $promoMessage . " Kunjungi {$destination->name} sekarang dan dapatkan pengalaman wisata terbaik!",
            'destination',
            $destination->id
        );
    }
    
    /**
     * Send reminder notification for upcoming booking
     */
    public function sendBookingReminder(Booking $booking)
    {
        $user = $booking->user;
        $destinationName = $booking->destination ? $booking->destination->name : 'Paket Wisata';
        
        return $this->sendNotification(
            $user,
            'Pengingat Booking',
            "Reminder: Booking Anda untuk {$destinationName} akan berlangsung besok pada tanggal {$booking->visit_date->format('d M Y')}. Pastikan QR Code tiket Anda sudah siap!",
            'booking'
        );
    }
    
    /**
     * Send post-visit review reminder
     */
    public function sendReviewReminder(Booking $booking)
    {
        $user = $booking->user;
        $destinationName = $booking->destination ? $booking->destination->name : 'Paket Wisata';
        
        return $this->sendNotification(
            $user,
            'Bagikan Pengalaman Anda',
            "Terima kasih telah mengunjungi {$destinationName}! Bagikan pengalaman Anda dengan memberikan review dan rating.",
            'review'
        );
    }
}