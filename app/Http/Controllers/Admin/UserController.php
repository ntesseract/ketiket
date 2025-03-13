<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        // Apply filters
        if ($request->has('role') && $request->role != 'all') {
            $query->where('role', $request->role);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone_number', 'like', "%$search%");
            });
        }
        
        // Sort
        $sort = $request->sort ?? 'name';
        $direction = $request->direction ?? 'asc';
        
        $query->orderBy($sort, $direction);
        
        $users = $query->paginate(20);
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'user'])],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'profile_picture' => ['nullable', 'image', 'max:5120'], // max 5MB
        ]);
        
        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $imagePath = $request->file('profile_picture')->store('profile-pictures', 'public');
            $validated['profile_picture'] = $imagePath;
        }
        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone_number' => $validated['phone_number'] ?? null,
            'address' => $validated['address'] ?? null,
            'profile_picture' => $validated['profile_picture'] ?? null,
            'email_verified_at' => now(),
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Display user details
     */
    public function show(User $user)
    {
        // Get user's bookings
        $bookings = Booking::where('user_id', $user->id)
            ->with(['destination', 'hotel', 'restaurant'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get user's wallet transactions
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get booking statistics
        $bookingStats = [
            'total' => Booking::where('user_id', $user->id)->count(),
            'completed' => Booking::where('user_id', $user->id)->where('status', 'completed')->count(),
            'pending' => Booking::where('user_id', $user->id)->where('status', 'pending')->count(),
            'cancelled' => Booking::where('user_id', $user->id)->where('status', 'cancelled')->count(),
        ];
        
        // Get wallet statistics
        $walletStats = [
            'balance' => $user->balance,
            'topUpCount' => WalletTransaction::where('user_id', $user->id)->where('type', 'top_up')->count(),
            'totalTopUp' => WalletTransaction::where('user_id', $user->id)->where('type', 'top_up')->sum('amount'),
            'paymentCount' => WalletTransaction::where('user_id', $user->id)->where('type', 'payment')->count(),
            'totalPayment' => WalletTransaction::where('user_id', $user->id)->where('type', 'payment')->sum('amount'),
        ];
        
        return view('admin.users.show', compact('user', 'bookings', 'transactions', 'bookingStats', 'walletStats'));
    }

    /**
     * Show the form for editing the user
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'user'])],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'profile_picture' => ['nullable', 'image', 'max:5120'], // max 5MB
        ]);
        
        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            
            $imagePath = $request->file('profile_picture')->store('profile-pictures', 'public');
            $validated['profile_picture'] = $imagePath;
        }
        
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone_number' => $validated['phone_number'] ?? null,
            'address' => $validated['address'] ?? null,
            'profile_picture' => $validated['profile_picture'] ?? $user->profile_picture,
        ]);
        
        // If password is provided, update it
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
            
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Delete the user
     */
    public function destroy(User $user)
    {
        // Check if user has bookings
        if (Booking::where('user_id', $user->id)->exists()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak dapat menghapus pengguna karena masih memiliki data booking.');
        }
        
        // Delete profile picture if exists
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Add wallet balance to user
     */
    public function addBalance(Request $request, User $user)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);
        
        $amount = $request->amount;
        $description = $request->description ?? 'Saldo ditambahkan oleh Admin';
        
        // Add balance to user's wallet
        $user->deposit($amount);
        
        // Create transaction record
        WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => 'top_up',
            'description' => $description,
            'reference_id' => 'ADM' . strtoupper(substr(md5(uniqid()), 0, 8)),
        ]);
        
        // Create notification
        \App\Models\UserNotification::create([
            'user_id' => $user->id,
            'title' => 'Saldo Ditambahkan',
            'message' => "Saldo sebesar Rp " . number_format($amount, 0, ',', '.') . " telah ditambahkan ke akun Anda. " . $description,
            'type' => 'wallet',
            'is_read' => false,
        ]);
        
        return redirect()->back()
            ->with('success', 'Saldo berhasil ditambahkan.');
    }
}