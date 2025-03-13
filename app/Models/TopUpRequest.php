<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Events\NewNotification;

class TopUpRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'note',
        'payment_method',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    /**
     * User who requested this top-up
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Admin who processed this request
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Check if status is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if status is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if status is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Create notification for admin about new top-up request
     */
    public function notifyAdmin()
    {
        // Get all admin users
        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            $notification = UserNotification::create([
                'user_id' => $admin->id,
                'title' => 'Permintaan Top Up Baru',
                'message' => "User {$this->user->name} meminta top up sebesar Rp " . number_format($this->amount, 0, ',', '.'),
                'type' => 'admin_topup',
                'is_read' => false,
            ]);
            
            // Broadcast notification for real-time updates
            broadcast(new NewNotification($notification))->toOthers();
        }
    }

    /**
     * Create notification for user about top-up request status
     */
    public function notifyUser($status)
    {
        $title = $status === 'approved' ? 'Top Up Disetujui' : 'Top Up Ditolak';
        $message = $status === 'approved' 
            ? "Permintaan top up Anda sebesar Rp " . number_format($this->amount, 0, ',', '.') . " telah disetujui."
            : "Permintaan top up Anda sebesar Rp " . number_format($this->amount, 0, ',', '.') . " ditolak. " . ($this->note ? "Catatan: {$this->note}" : "");
        
        $notification = UserNotification::create([
            'user_id' => $this->user_id,
            'title' => $title,
            'message' => $message,
            'type' => 'wallet',
            'is_read' => false,
        ]);
        
        // Broadcast notification for real-time updates
        broadcast(new NewNotification($notification))->toOthers();
    }
}