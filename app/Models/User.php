<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Bavix\Wallet\Traits\HasWallet;
use Bavix\Wallet\Interfaces\Wallet;

class User extends Authenticatable implements Wallet
{
    use HasFactory, Notifiable;
    use HasWallet; // Trait dari package bavix/laravel-wallet

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'address',
        'profile_picture',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * User's bookings
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * User's custom wallet transactions (jika Anda memiliki model WalletTransaction sendiri)
     * Ganti nama metode untuk menghindari konflik dengan package
     */
    public function customWalletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * User's reviews
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * User's favorites
     */
    public function favorites()
    {
        return $this->belongsToMany(Destination::class, 'favorites', 'user_id', 'destination_id')
            ->withTimestamps();
    }

    /**
     * User's notifications
     */
    public function userNotifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    /**
     * Get formatted balance with currency symbol
     * 
     * @return string
     */
    public function getFormattedBalanceAttribute()
    {
        // Menggunakan balance dari package bavix/laravel-wallet
        return 'Rp ' . number_format($this->balanceInt / 100, 0, ',', '.');
    }
}