<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WalletTransaction;
use App\Models\TopUpRequest;
use App\Models\UserNotification;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\NotificationService;

class WalletController extends Controller
{
    protected $notificationService;
    
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    
    /**
     * Display wallet information
     */
    public function index()
    {
        $user = Auth::user();
        
        // Dapatkan saldo dari database
        $balance = DB::table('wallets')->where('user_id', $user->id)->value('balance') ?? 0;
        
        // Dapatkan transaksi dompet dari model WalletTransaction
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        // Dapatkan permintaan top up yang masih pending
        $pendingTopUps = TopUpRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Dapatkan riwayat permintaan top up yang sudah selesai
        $completedTopUps = TopUpRequest::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('wallet.index', compact('balance', 'transactions', 'pendingTopUps', 'completedTopUps'));
    }
    
    /**
     * Show the form for topping up wallet
     */
    public function showTopUpForm()
    {
        return view('wallet.topup');
    }
    
    /**
     * Show form untuk request top up ke admin
     */
    public function showRequestTopUpForm()
    {
        return view('wallet.request-topup');
    }
    
    /**
     * Proses permintaan top up ke admin
     */
    public function requestTopUp(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'note' => 'nullable|string|max:255',
        ]);
        
        $user = Auth::user();
        $amount = $request->amount;
        $referenceId = 'REQ' . strtoupper(Str::random(8));
        
        try {
            DB::beginTransaction();
            
            // Buat permintaan top up baru
            $topUpRequest = TopUpRequest::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'note' => $request->note,
                'status' => 'pending',
                'reference_id' => $referenceId,
            ]);
            
            // Buat notifikasi untuk admin
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                UserNotification::create([
                    'user_id' => $admin->id,
                    'title' => 'Permintaan Top Up Baru',
                    'message' => "User {$user->name} meminta top up sebesar Rp " . number_format($amount, 0, ',', '.'),
                    'type' => 'admin_topup',
                    'is_read' => false,
                ]);
            }
            
            // Buat notifikasi untuk user bahwa permintaan telah dikirim
            UserNotification::create([
                'user_id' => $user->id,
                'title' => 'Permintaan Top Up Terkirim',
                'message' => "Permintaan top up sebesar Rp " . number_format($amount, 0, ',', '.') . " telah dikirim dan menunggu persetujuan admin.",
                'type' => 'wallet',
                'is_read' => false,
            ]);
            
            DB::commit();
            
            return redirect()->route('wallet.index')
                ->with('success', 'Permintaan top up telah dikirim dan sedang menunggu persetujuan admin.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengirim permintaan top up: ' . $e->getMessage());
        }
    }
    
    /**
     * Cancel a pending top-up request
     */
    public function cancelTopUpRequest($id)
    {
        $user = Auth::user();
        $topUpRequest = TopUpRequest::findOrFail($id);
        
        // Pastikan request ini milik user yang sedang login
        if ($topUpRequest->user_id !== $user->id) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki akses untuk membatalkan permintaan ini.');
        }
        
        // Pastikan request masih dalam status pending
        if ($topUpRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Permintaan top up yang sudah diproses tidak dapat dibatalkan.');
        }
        
        try {
            DB::beginTransaction();
            
            // Update status menjadi rejected
            $topUpRequest->update([
                'status' => 'rejected',
                'note' => 'Dibatalkan oleh pengguna',
                'processed_at' => now(),
            ]);
            
            // Buat notifikasi untuk user bahwa permintaan telah dibatalkan
            UserNotification::create([
                'user_id' => $user->id,
                'title' => 'Permintaan Top Up Dibatalkan',
                'message' => "Permintaan top up sebesar Rp " . number_format($topUpRequest->amount, 0, ',', '.') . " telah dibatalkan.",
                'type' => 'wallet',
                'is_read' => false,
            ]);
            
            DB::commit();
            
            return redirect()->route('wallet.index')
                ->with('success', 'Permintaan top up berhasil dibatalkan.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat membatalkan permintaan: ' . $e->getMessage());
        }
    }
    
    /**
     * Process top-up request
     */
    public function topUp(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'payment_method' => 'required|in:bank_transfer,credit_card,e_wallet',
        ]);
        
        $user = Auth::user();
        $amount = $request->amount;
        $referenceId = 'TOP' . strtoupper(Str::random(8));
        
        // In a real application, this would be integrated with a payment gateway
        // For demo purposes, we'll simulate a successful payment
        
        try {
            // Begin transaction
            DB::beginTransaction();
            
            // Ambil saldo terkini dari tabel wallets
            $wallet = DB::table('wallets')->where('user_id', $user->id)->first();
            
            if ($wallet) {
                // Update saldo yang sudah ada
                DB::table('wallets')
                    ->where('user_id', $user->id)
                    ->increment('balance', $amount);
            } else {
                // Buat record wallet baru
                DB::table('wallets')->insert([
                    'user_id' => $user->id,
                    'balance' => $amount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Catat transaksi di tabel transactions
            DB::table('transactions')->insert([
                'payable_type' => 'App\\Models\\User',
                'payable_id' => $user->id,
                'wallet_id' => $wallet ? $wallet->id : DB::getPdo()->lastInsertId(),
                'type' => 'deposit',
                'amount' => $amount,
                'confirmed' => true,
                'meta' => json_encode([
                    'description' => 'Top up via ' . $request->payment_method,
                    'reference' => $referenceId,
                ]),
                'uuid' => (string) Str::uuid(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Buat record di tabel lokal WalletTransaction untuk kebutuhan aplikasi
            WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'top_up',
                'description' => 'Top up via ' . $request->payment_method,
                'reference_id' => $referenceId,
                'status' => 'success',
            ]);
            
            // Create notification
            UserNotification::create([
                'user_id' => $user->id,
                'title' => 'Top Up Berhasil',
                'message' => "Selamat! Top up saldo sebesar Rp " . number_format($amount, 0, ',', '.') . " telah berhasil.",
                'type' => 'wallet',
                'is_read' => false,
            ]);
            
            DB::commit();
            
            return redirect()->route('wallet.index')
                ->with('success', 'Top up berhasil. Saldo telah ditambahkan ke akun Anda.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat melakukan top up: ' . $e->getMessage());
        }
    }
    
    /**
     * Process payment for a booking
     */
    public function processPayment($bookingId)
    {
        $user = Auth::user();
        $booking = Booking::findOrFail($bookingId);
        
        // Check if booking belongs to user
        if ($booking->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke booking ini.');
        }
        
        // Check if booking is already paid
        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Booking ini sudah diproses sebelumnya.');
        }
        
        // Dapatkan saldo terkini
        $wallet = DB::table('wallets')->where('user_id', $user->id)->first();
        $currentBalance = $wallet ? $wallet->balance : 0;
        
        // Check if user has enough balance
        if ($currentBalance < $booking->total_price) {
            return redirect()->route('wallet.topup')
                ->with('error', 'Saldo tidak mencukupi. Silakan top up terlebih dahulu.');
        }
        
        // Process payment
        try {
            // Begin transaction
            DB::beginTransaction();
            
            // Kurangi saldo di wallet
            DB::table('wallets')
                ->where('user_id', $user->id)
                ->decrement('balance', $booking->total_price);
            
            // ID transaksi
            $referenceId = 'PAY' . strtoupper(Str::random(8));
            
            // Catat transaksi di tabel transactions
            DB::table('transactions')->insert([
                'payable_type' => 'App\\Models\\User',
                'payable_id' => $user->id,
                'wallet_id' => $wallet->id,
                'type' => 'withdraw',
                'amount' => $booking->total_price,
                'confirmed' => true,
                'meta' => json_encode([
                    'description' => 'Pembayaran untuk booking #' . $booking->id,
                    'reference' => $referenceId,
                ]),
                'uuid' => (string) Str::uuid(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Buat record di tabel lokal WalletTransaction untuk kebutuhan aplikasi
            WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $booking->total_price,
                'type' => 'payment',
                'description' => 'Pembayaran untuk booking #' . $booking->id,
                'reference_id' => $referenceId,
                'status' => 'success',
            ]);
            
            // Update booking status
            $booking->status = 'confirmed';
            $booking->save();
            
            // Generate QR code if method exists
            if (method_exists($booking, 'generateQRCode')) {
                $qrCode = $booking->generateQRCode();
            }
            
            // Create notification
            UserNotification::create([
                'user_id' => $user->id,
                'title' => 'Pembayaran Berhasil',
                'message' => "Pembayaran untuk booking #{$booking->id} telah berhasil. Tiket digital Anda telah siap.",
                'type' => 'booking',
                'is_read' => false,
            ]);
            
            DB::commit();
            
            return redirect()->route('booking.show', $booking->id)
                ->with('success', 'Pembayaran berhasil. Tiket digital Anda telah siap.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memproses pembayaran: ' . $e->getMessage());
        }
    }
    
    /**
     * Process refund for a cancelled booking
     */
    public function processRefund($bookingId)
    {
        $user = Auth::user();
        $booking = Booking::findOrFail($bookingId);
        
        // Only admin can process refunds
        if (!$user->role || $user->role !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melakukan refund.');
        }
        
        // Check if booking can be refunded
        if ($booking->status !== 'confirmed') {
            return redirect()->back()->with('error', 'Booking ini tidak dapat di-refund.');
        }
        
        // Process refund
        try {
            // Begin transaction
            DB::beginTransaction();
            
            $bookingUser = $booking->user;
            
            // Dapatkan wallet pengguna
            $wallet = DB::table('wallets')->where('user_id', $bookingUser->id)->first();
            
            if ($wallet) {
                // Update saldo yang sudah ada
                DB::table('wallets')
                    ->where('user_id', $bookingUser->id)
                    ->increment('balance', $booking->total_price);
            } else {
                // Buat record wallet baru jika belum ada
                DB::table('wallets')->insert([
                    'user_id' => $bookingUser->id,
                    'balance' => $booking->total_price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // ID transaksi
            $referenceId = 'REF' . strtoupper(Str::random(8));
            
            // Catat transaksi di tabel transactions
            DB::table('transactions')->insert([
                'payable_type' => 'App\\Models\\User',
                'payable_id' => $bookingUser->id,
                'wallet_id' => $wallet ? $wallet->id : DB::getPdo()->lastInsertId(),
                'type' => 'deposit',
                'amount' => $booking->total_price,
                'confirmed' => true,
                'meta' => json_encode([
                    'description' => 'Refund untuk booking #' . $booking->id,
                    'reference' => $referenceId,
                ]),
                'uuid' => (string) Str::uuid(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Buat record di tabel lokal WalletTransaction untuk kebutuhan aplikasi
            WalletTransaction::create([
                'user_id' => $bookingUser->id,
                'amount' => $booking->total_price,
                'type' => 'refund',
                'description' => 'Refund untuk booking #' . $booking->id,
                'reference_id' => $referenceId,
                'status' => 'success',
            ]);
            
            // Update booking status
            $booking->status = 'cancelled';
            $booking->save();
            
            // Create notification
            UserNotification::create([
                'user_id' => $bookingUser->id,
                'title' => 'Refund Berhasil',
                'message' => "Refund untuk booking #{$booking->id} telah berhasil diproses. Saldo telah ditambahkan ke wallet Anda.",
                'type' => 'wallet',
                'is_read' => false,
            ]);
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Refund berhasil diproses.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memproses refund: ' . $e->getMessage());
        }
    }
}