<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'description',
        'reference_id',
    ];

    /**
     * Get the user who made this transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for top-up transactions
     */
    public function scopeTopUp($query)
    {
        return $query->where('type', 'top_up');
    }

    /**
     * Scope for payment transactions
     */
    public function scopePayment($query)
    {
        return $query->where('type', 'payment');
    }

    /**
     * Scope for refund transactions
     */
    public function scopeRefund($query)
    {
        return $query->where('type', 'refund');
    }
}