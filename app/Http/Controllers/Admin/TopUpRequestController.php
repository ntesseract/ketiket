<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TopUpRequest;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TopUpRequestController extends Controller
{
    /**
     * Display all top-up requests
     */
    public function index()
    {
        // Permintaan yang belum diproses
        $pendingRequests = TopUpRequest::with('user')
            ->pending()
            ->latest()
            ->get();
        
        // Permintaan yang sudah diproses
        $completedRequests = TopUpRequest::with('user', 'admin')
            ->completed()
            ->latest()
            ->paginate(15);
        
        return view('admin.topup.index', compact('pendingRequests', 'completedRequests'));
    }
    
    /**
     * Show details of a specific top-up request
     */
    public function show(TopUpRequest $topUpRequest)
    {
        $topUpRequest->load('user', 'admin');
        
        return view('admin.topup.show', compact('topUpRequest'));
    }
    
    /**
     * Approve a top-up request
     */
    public function approve(TopUpRequest $topUpRequest, Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'note' => 'nullable|string|max:255',
        ]);
        
        // Pastikan request masih pending
        if ($topUpRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }
        
        // Proses approve
        DB::beginTransaction();
        try {
            // Update status request
            $topUpRequest->update([
                'status' => 'approved',
                'note' => $validated['note'] ?? $topUpRequest->note,
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);
            
            $user = User::findOrFail($topUpRequest->user_id);
            $amount = $topUpRequest->amount;
            
            // Generate reference ID unik
            $referenceId = 'APP' . strtoupper(Str::random(8));
            
            // Cek wallet user
            $wallet = DB::table('wallets')->where('user_id', $user->id)->first();
            
            if ($wallet) {
                // Update existing wallet
                DB::table('wallets')
                    ->where('user_id', $user->id)
                    ->increment('balance', $amount);
            } else {
                // Create new wallet
                DB::table('wallets')->insert([
                    'user_id' => $user->id,
                    'balance' => $amount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Record transaction in bavix/laravel-wallet table
            DB::table('transactions')->insert([
                'payable_type' => 'App\\Models\\User',
                'payable_id' => $user->id,
                'wallet_id' => $wallet ? $wallet->id : DB::getPdo()->lastInsertId(),
                'type' => 'deposit',
                'amount' => $amount,
                'confirmed' => true,
                'meta' => json_encode([
                    'description' => 'Top up disetujui oleh admin',
                    'reference' => $referenceId,
                    'top_up_request_id' => $topUpRequest->id,
                ]),
                'uuid' => (string) Str::uuid(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Create record in WalletTransaction table
            WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'top_up',
                'description' => 'Top up disetujui oleh admin',
                'reference_id' => $referenceId,
                'status' => 'success',
            ]);
            
            // Create notification for user
            UserNotification::create([
                'user_id' => $user->id,
                'title' => 'Top Up Disetujui',
                'message' => "Permintaan top up sebesar Rp " . number_format($amount, 0, ',', '.') . " telah disetujui dan ditambahkan ke saldo Anda.",
                'type' => 'wallet',
                'is_read' => false,
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.topup.index')
                ->with('success', 'Permintaan top up berhasil disetujui dan saldo telah ditambahkan.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memproses permintaan: ' . $e->getMessage());
        }
    }
    
    /**
     * Reject a top-up request
     */
    public function reject(TopUpRequest $topUpRequest, Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'note' => 'required|string|max:255',
        ]);
        
        // Pastikan request masih pending
        if ($topUpRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }
        
        // Proses reject
        DB::beginTransaction();
        try {
            // Update status request
            $topUpRequest->update([
                'status' => 'rejected',
                'note' => $validated['note'],
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);
            
            // Create notification for user
            UserNotification::create([
                'user_id' => $topUpRequest->user_id,
                'title' => 'Top Up Ditolak',
                'message' => "Permintaan top up sebesar Rp " . number_format($topUpRequest->amount, 0, ',', '.') . " ditolak dengan alasan: " . $validated['note'],
                'type' => 'wallet',
                'is_read' => false,
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.topup.index')
                ->with('success', 'Permintaan top up telah ditolak.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memproses permintaan: ' . $e->getMessage());
        }
    }
}