<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'message',
        'is_from_admin',
        'is_read',
    ];

    protected $casts = [
        'is_from_admin' => 'boolean',
        'is_read' => 'boolean',
    ];

    /**
     * Get the user who sent or received this message
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who sent or received this message
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Scope for unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for user messages
     */
    public function scopeFromUser($query)
    {
        return $query->where('is_from_admin', false);
    }

    /**
     * Scope for admin messages
     */
    public function scopeFromAdmin($query)
    {
        return $query->where('is_from_admin', true);
    }

    /**
     * Mark message as read
     */
    public function markAsRead()
    {
        $this->is_read = true;
        $this->save();
        
        return $this;
    }
    
    /**
     * Get conversation between user and admin
     */
    public static function getConversation($userId, $adminId = null)
    {
        $query = self::where('user_id', $userId);
        
        if ($adminId) {
            $query->where('admin_id', $adminId);
        }
        
        return $query->orderBy('created_at', 'asc')->get();
    }
    
    /**
     * Get unread messages count for a user
     */
    public static function unreadCountForUser($userId)
    {
        return self::where('user_id', $userId)
            ->where('is_from_admin', true)
            ->where('is_read', false)
            ->count();
    }
    
    /**
     * Get unread messages count for an admin from all users
     */
    public static function unreadCountForAdmin($adminId)
    {
        return self::where('admin_id', $adminId)
            ->where('is_from_admin', false)
            ->where('is_read', false)
            ->count();
    }
}