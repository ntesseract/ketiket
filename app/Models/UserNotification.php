<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\NewNotification;

class UserNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Get the user who received this notification
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->is_read = true;
        $this->save();
        
        return $this;
    }
    
    /**
     * Scope for notifications by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
    
    /**
     * Create a booking notification
     */
    public static function createBookingNotification($user, $booking, $title, $message)
    {
        $notification = self::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => 'booking',
            'is_read' => false,
        ]);
        
        // Broadcast event for real-time updates
        broadcast(new NewNotification($notification))->toOthers();
        
        return $notification;
    }
    
    /**
     * Create a wallet notification
     */
    public static function createWalletNotification($user, $transaction, $title, $message)
    {
        $notification = self::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => 'wallet',
            'is_read' => false,
        ]);
        
        // Broadcast event for real-time updates
        broadcast(new NewNotification($notification))->toOthers();
        
        return $notification;
    }
    
    /**
     * Create a promo notification
     */
    public static function createPromoNotification($user, $title, $message)
    {
        $notification = self::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => 'promo',
            'is_read' => false,
        ]);
        
        // Broadcast event for real-time updates
        broadcast(new NewNotification($notification))->toOthers();
        
        return $notification;
    }
}