{{-- resources/views/wallet/index.blade.php --}}
@extends('layouts.app')

@section('title', 'E-Wallet Saya')

@push('styles')
<style>
    /* Animasi */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideInUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(30px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .fade-in {
        opacity: 0;
        animation: fadeIn 0.6s ease-in-out forwards;
    }
    
    .slide-in {
        opacity: 0;
        animation: slideInUp 0.6s ease-in-out forwards;
    }
    
    .slide-in-right {
        opacity: 0;
        animation: slideInRight 0.6s ease-in-out forwards;
    }
    
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    .delay-400 { animation-delay: 0.4s; }
    .delay-500 { animation-delay: 0.5s; }
    
    /* Hover Effect */
    .hover-scale {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }
    
    .hover-scale:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    /* Loading Spinner */
    .loading-spinner {
        border: 4px solid rgba(0, 0, 0, 0.1);
        border-radius: 50%;
        border-top: 4px solid #3498db;
        width: 36px;
        height: 36px;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Parallax Effect */
    .parallax {
        background-attachment: fixed;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
    }
    
    /* Tab animation */
    .tab-content {
        transition: all 0.3s ease-in-out;
    }
    
    .tab-content.hidden {
        opacity: 0;
        transform: translateY(10px);
    }
</style>
@endpush

@section('content')
<div id="loading-overlay" class="fixed inset-0 flex items-center justify-center z-50 bg-white bg-opacity-90">
    <div class="text-center">
        <div class="loading-spinner mx-auto mb-4"></div>
        <p class="text-gray-600">Memuat informasi wallet...</p>
    </div>
</div>

<div class="container py-6 opacity-0" id="main-content">
    <div class="max-w-6xl mx-auto">
        
        @if(session('success'))
        <div class="bg-green-50 p-4 rounded-lg mb-6 border-l-4 border-green-500 opacity-0" data-animate="fade-in">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-50 p-4 rounded-lg mb-6 border-l-4 border-red-500 opacity-0" data-animate="fade-in">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Balance Card -->
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl shadow-lg p-6 mb-6 text-white opacity-0 hover-scale" data-animate="slide-in">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-medium opacity-75">Saldo E-Wallet</h3>
                    <div class="text-3xl md:text-4xl font-bold mt-2">Rp {{ number_format($balance, 0, ',', '.') }}</div>
                    <p class="mt-2 text-sm opacity-75">Terakhir diperbarui: {{ now()->format('d M Y H:i') }}</p>
                </div>
                <div class="flex flex-col items-end space-y-2">
                    <a href="{{ route('wallet.request.topup.form') }}" class="bg-white text-blue-600 rounded-lg py-2 px-4 font-medium hover:bg-gray-100 transition-colors flex items-center hover-scale">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Request Top Up
                    </a>
                    <a href="{{ route('wallet.topup') }}" class="text-white bg-white bg-opacity-20 rounded-lg py-2 px-4 font-medium hover:bg-opacity-30 transition-colors flex items-center hover-scale">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        Top Up Instan
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Tabs Navigation -->
        <div class="mb-6 border-b border-gray-200 opacity-0" data-animate="fade-in" data-delay="100" x-data="{ activeTab: 'transactions' }">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                <li class="mr-2">
                    <a href="#" class="inline-block p-4 border-b-2 rounded-t-lg" 
                       :class="activeTab === 'transactions' ? 'text-blue-600 border-blue-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300'"
                       @click.prevent="activeTab = 'transactions'">
                        <svg class="w-5 h-5 mr-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        Riwayat Transaksi
                    </a>
                </li>
                <li class="mr-2">
                    <a href="#" class="inline-block p-4 border-b-2 rounded-t-lg" 
                       :class="activeTab === 'pending' ? 'text-blue-600 border-blue-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300'"
                       @click.prevent="activeTab = 'pending'">
                        <svg class="w-5 h-5 mr-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Pending Request
                        @if(count($pendingTopUps) > 0)
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full ml-2">{{ count($pendingTopUps) }}</span>
                        @endif
                    </a>
                </li>
                <li class="mr-2">
                    <a href="#" class="inline-block p-4 border-b-2 rounded-t-lg" 
                       :class="activeTab === 'history' ? 'text-blue-600 border-blue-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300'"
                       @click.prevent="activeTab = 'history'">
                        <svg class="w-5 h-5 mr-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Riwayat Request
                    </a>
                </li>
            </ul>
        </div>

        <!-- Tab Contents -->
        <div class="opacity-0" data-animate="fade-in" data-delay="200">
            <!-- Transactions Tab -->
            <div x-show="activeTab === 'transactions'" x-transition class="tab-content bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    Riwayat Transaksi
                </h3>
                
                @if(count($transactions) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($transactions as $transaction)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4 text-sm text-gray-500">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                <td class="py-3 px-4">
                                    @if($transaction->type == 'top_up')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Top Up</span>
                                    @elseif($transaction->type == 'payment')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Pembayaran</span>
                                    @elseif($transaction->type == 'refund')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">Refund</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">{{ $transaction->type }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-500">{{ $transaction->description }}</td>
                                <td class="py-3 px-4 text-sm">
                                    @if($transaction->type == 'payment')
                                        <span class="text-red-600 font-medium">- Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-green-600 font-medium">+ Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($transaction->status == 'success')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Berhasil</span>
                                    @elseif($transaction->status == 'pending')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    @elseif($transaction->status == 'failed')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Gagal</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">{{ $transaction->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-6">
                    {{ $transactions->links() }}
                </div>
                @else
                <div class="flex items-center justify-center py-12 bg-gray-50 rounded-lg">
                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada transaksi</h3>
                        <p class="mt-1 text-sm text-gray-500">Mulai transaksi pertama Anda dengan melakukan top up saldo.</p>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Pending Requests Tab -->
            <div x-show="activeTab === 'pending'" x-transition class="tab-content bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Permintaan Top Up Tertunda
                </h3>
                
                @if(count($pendingTopUps) > 0)
                <div class="space-y-4">
                    @foreach($pendingTopUps as $request)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 hover-scale">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-medium text-gray-900">Permintaan #{{ $request->id }}</h4>
                                <p class="text-sm text-gray-600 mt-1">Dibuat: {{ $request->created_at->format('d M Y H:i') }}</p>
                                <div class="mt-2">
                                    <span class="text-lg font-semibold text-gray-900">Rp {{ number_format($request->amount, 0, ',', '.') }}</span>
                                </div>
                                @if($request->note)
                                <p class="mt-2 text-sm text-gray-600">Catatan: {{ $request->note }}</p>
                                @endif
                            </div>
                            <div class="flex flex-col items-end space-y-2">
                                <span class="px-3 py-1 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800">Menunggu Persetujuan</span>
                                <form action="{{ route('wallet.cancel.topup', $request->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan permintaan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Batalkan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="flex items-center justify-center py-12 bg-gray-50 rounded-lg">
                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada permintaan tertunda</h3>
                        <p class="mt-1 text-sm text-gray-500">Semua permintaan top up Anda telah diproses.</p>
                        <div class="mt-4">
                            <a href="{{ route('wallet.request.topup.form') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Buat Permintaan Top Up
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Request History Tab -->
            <div x-show="activeTab === 'history'" x-transition class="tab-content bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Riwayat Permintaan Top Up
                </h3>
                
                @if(count($completedTopUps) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diproses Pada</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($completedTopUps as $request)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4 text-sm text-gray-500">{{ $request->created_at->format('d M Y H:i') }}</td>
                                <td class="py-3 px-4 text-sm font-medium text-gray-900">Rp {{ number_format($request->amount, 0, ',', '.') }}</td>
                                <td class="py-3 px-4">
                                    @if($request->status == 'approved')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Disetujui</span>
                                    @elseif($request->status == 'rejected')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Ditolak</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">{{ $request->status }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-500">{{ $request->note ?? '-' }}</td>
                                <td class="py-3 px-4 text-sm text-gray-500">{{ $request->processed_at ? $request->processed_at->format('d M Y H:i') : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-6">
                    {{ $completedTopUps->links() }}
                </div>
                @else
                <div class="flex items-center justify-center py-12 bg-gray-50 rounded-lg">
                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada riwayat permintaan</h3>
                        <p class="mt-1 text-sm text-gray-500">Riwayat permintaan top up yang telah diproses akan muncul di sini.</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Parallax Section -->
        <div class="mt-8 rounded-lg overflow-hidden shadow-lg opacity-0" data-animate="fade-in" data-delay="300">
            <div class="parallax p-8 flex items-center justify-center" style="background-image: url('{{ asset('images/wallet-bg.jpg') }}'); height: 200px;">
                <div class="text-center bg-white bg-opacity-90 p-6 rounded-lg">
                    <h3 class="text-xl font-bold text-gray-800">Kelola Keuangan Anda dengan E-Wallet</h3>
                    <p class="text-gray-600 mt-2">Top up saldo kapan saja dan nikmati kemudahan bertransaksi untuk semua kebutuhan perjalanan Anda</p>
                    <div class="mt-4">
                        <a href="{{ route('wallet.request.topup.form') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 hover-scale">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Request Top Up
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simulasikan loading
    setTimeout(function() {
        document.getElementById('loading-overlay').style.display = 'none';
        document.getElementById('main-content').style.opacity = '1';
        
        // Animate elements dengan fade-in
        const fadeElements = document.querySelectorAll('[data-animate="fade-in"]');
        fadeElements.forEach((element) => {
            setTimeout(() => {
                element.style.animation = 'fadeIn 0.5s ease-in-out forwards';
            }, element.getAttribute('data-delay') || 0);
        });
        
        // Animate elements dengan slide-in
        const slideElements = document.querySelectorAll('[data-animate="slide-in"]');
        slideElements.forEach((element) => {
            setTimeout(() => {
                element.style.animation = 'slideInUp 0.5s ease-in-out forwards';
            }, element.getAttribute('data-delay') || 0);
        });
        
        // Animate elements dengan slide-in-right
        const slideRightElements = document.querySelectorAll('[data-animate="slide-in-right"]');
        slideRightElements.forEach((element) => {
            setTimeout(() => {
                element.style.animation = 'slideInRight 0.5s ease-in-out forwards';
            }, element.getAttribute('data-delay') || 0);
        });
    }, 800);
    
    // Parallax effect on scroll
    window.addEventListener('scroll', function() {
        const scrollPosition = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.parallax');
        parallaxElements.forEach(element => {
            element.style.backgroundPositionY = -scrollPosition * 0.15 + 'px';
        });
    });
});
</script>
@endpush
@endsection