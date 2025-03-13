@extends('layouts.admin')

@section('title', 'Detail Pengguna')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Pengguna</h1>
            <p class="mt-1 text-sm text-gray-600">
                Detail lengkap dan informasi terkait pengguna
            </p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
        </div>
    </div>
    
    <!-- User Profile -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-indigo-700 h-32 relative">
            <div class="absolute -bottom-12 left-6 bg-white p-1.5 rounded-full">
                @if($user->profile_picture)
                    <img src="{{ Storage::url($user->profile_picture) }}" alt="{{ $user->name }}" class="h-24 w-24 rounded-full object-cover">
                @else
                    <div class="h-24 w-24 rounded-full bg-indigo-100 flex items-center justify-center">
                        <span class="text-indigo-800 text-3xl font-semibold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="p-6 pt-16">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role == 'admin' ? 'bg-indigo-100 text-indigo-800' : 'bg-green-100 text-green-800' }}">
                            {{ $user->role == 'admin' ? 'Administrator' : 'User' }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2 {{ $user->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $user->email_verified_at ? 'Terverifikasi' : 'Belum Verifikasi' }}
                        </span>
                    </p>
                    
                    <div class="mt-6 space-y-3">
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-gray-400 w-6"></i>
                            <span class="ml-2 text-gray-800">{{ $user->email }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-phone text-gray-400 w-6"></i>
                            <span class="ml-2 text-gray-800">{{ $user->phone_number ?: 'Tidak ada' }}</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt text-gray-400 w-6 mt-1"></i>
                            <span class="ml-2 text-gray-800">{{ $user->address ?: 'Tidak ada' }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt text-gray-400 w-6"></i>
                            <span class="ml-2 text-gray-800">Terdaftar {{ $user->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-semibold text-gray-800 mb-2">Saldo Wallet</h3>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-indigo-600">Rp {{ number_format($user->balance, 0, ',', '.') }}</span>
                            <button onclick="toggleModal('addBalanceModal')" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i class="fas fa-plus mr-1"></i> Tambah
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-sm font-semibold text-gray-800">Total Booking</h3>
                            <div class="mt-2 flex justify-between items-end">
                                <span class="text-xl font-bold text-gray-900">{{ $bookingStats['total'] }}</span>
                                <i class="fas fa-ticket-alt text-indigo-400"></i>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-sm font-semibold text-gray-800">Booking Selesai</h3>
                            <div class="mt-2 flex justify-between items-end">
                                <span class="text-xl font-bold text-green-600">{{ $bookingStats['completed'] }}</span>
                                <i class="fas fa-check-circle text-green-400"></i>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-sm font-semibold text-gray-800">Top Up</h3>
                            <div class="mt-2 flex justify-between items-end">
                                <span class="text-xl font-bold text-blue-600">{{ $walletStats['topUpCount'] }}</span>
                                <i class="fas fa-arrow-circle-up text-blue-400"></i>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-sm font-semibold text-gray-800">Transaksi</h3>
                            <div class="mt-2 flex justify-between items-end">
                                <span class="text-xl font-bold text-purple-600">{{ $walletStats['paymentCount'] }}</span>
                                <i class="fas fa-exchange-alt text-purple-400"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabs -->
    <div x-data="{ tab: 'bookings' }">
        <!-- Tab Navigation -->
        <div class="flex border-b border-gray-200">
            <button @click="tab = 'bookings'" class="px-4 py-2 focus:outline-none" :class="tab === 'bookings' ? 'border-b-2 border-indigo-500 text-indigo-600 font-medium' : 'text-gray-500 hover:text-gray-700'">
                Riwayat Booking
            </button>
            <button @click="tab = 'wallet'" class="px-4 py-2 focus:outline-none" :class="tab === 'wallet' ? 'border-b-2 border-indigo-500 text-indigo-600 font-medium' : 'text-gray-500 hover:text-gray-700'">
                Transaksi Wallet
            </button>
        </div>
        
        <!-- Bookings History Tab -->
        <div x-show="tab === 'bookings'" class="bg-white shadow-md rounded-lg overflow-hidden mt-4">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Booking</h3>
                
                @if(count($bookings) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destinasi/Paket</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($bookings as $booking)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            #{{ $booking->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($booking->is_package)
                                                    <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                                        <i class="fas fa-suitcase text-indigo-500"></i>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">Paket Wisata</div>
                                                        <div class="text-sm text-gray-500">{{ $booking->destination->name ?? 'Multiple Destinations' }}</div>
                                                    </div>
                                                @else
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        @if($booking->destination && $booking->destination->image)
                                                            <img class="h-10 w-10 rounded-lg object-cover" src="{{ Storage::url($booking->destination->image) }}" alt="{{ $booking->destination->name }}">
                                                        @else
                                                            <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                                <i class="fas fa-map-marker-alt text-blue-500"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $booking->destination->name ?? 'Destinasi' }}</div>
                                                        <div class="text-sm text-gray-500">{{ Str::limit($booking->destination->location ?? '', 30) }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $booking->visit_date->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $booking->number_of_tickets }} Tiket
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($booking->status == 'confirmed') bg-green-100 text-green-800
                                                @elseif($booking->status == 'completed') bg-blue-100 text-blue-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.bookings.show', $booking->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                                <i class="fas fa-eye"></i> Lihat
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 flex justify-end">
                        <a href="{{ route('admin.bookings.index', ['user_id' => $user->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            Lihat semua booking <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 mb-2">
                            <i class="fas fa-ticket-alt text-4xl"></i>
                        </div>
                        <p class="text-gray-500">Pengguna belum memiliki riwayat booking</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Wallet Transactions Tab -->
        <div x-show="tab === 'wallet'" class="bg-white shadow-md rounded-lg overflow-hidden mt-4">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Transaksi Wallet</h3>
                
                @if(count($transactions) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referensi</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($transactions as $transaction)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            #{{ $transaction->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transaction->created_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $transaction->type == 'top_up' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ $transaction->type == 'top_up' ? 'Top Up' : 'Pembayaran' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm 
                                            {{ $transaction->type == 'top_up' ? 'text-green-600 font-medium' : 'text-red-600 font-medium' }}">
                                            {{ $transaction->type == 'top_up' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transaction->reference_id ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $transaction->description ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 flex justify-end">
                        <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800">
                            Lihat semua transaksi <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 mb-2">
                            <i class="fas fa-wallet text-4xl"></i>
                        </div>
                        <p class="text-gray-500">Pengguna belum memiliki riwayat transaksi wallet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Balance Modal -->
<div id="addBalanceModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-auto transform transition-all p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Tambah Saldo Wallet</h3>
            <button onclick="toggleModal('addBalanceModal')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('admin.users.add-balance', $user->id) }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Jumlah <span class="text-red-500">*</span></label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">Rp</span>
                    </div>
                    <input type="number" name="amount" id="amount" required min="1"
                        class="w-full pl-12 pr-12 rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="100000">
                </div>
            </div>
            
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" id="description" rows="2"
                    class="w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Alasan penambahan saldo (opsional)"></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="toggleModal('addBalanceModal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none">
                    <i class="fas fa-plus mr-1"></i> Tambah Saldo
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Toggle modal visibility
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                const firstInput = modal.querySelector('input');
                if (firstInput) firstInput.focus();
            }, 100);
        } else {
            modal.classList.add('hidden');
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Handle ESC key to close modals
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modals = document.querySelectorAll('.fixed.inset-0:not(.hidden)');
                modals.forEach(modal => {
                    modal.classList.add('hidden');
                });
            }
        });
        
        // Close modal when clicking outside
        const modals = document.querySelectorAll('.fixed.inset-0');
        modals.forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    modal.classList.add('hidden');
                }
            });
        });
    });
</script>
@endpush
@endsection