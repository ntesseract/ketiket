@extends('layouts.admin')

@section('title', 'Detail Booking #' . $booking->id)
@section('header', 'Detail Booking #' . $booking->id)

@push('styles')
<style>
    /* Animation */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .fade-in {
        animation: fadeIn 0.6s ease-in-out forwards;
    }
    
    @keyframes slideInUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .slide-in-up {
        animation: slideInUp 0.5s ease-in-out forwards;
    }
    
    .slide-delay-1 { animation-delay: 0.1s; }
    .slide-delay-2 { animation-delay: 0.2s; }
    .slide-delay-3 { animation-delay: 0.3s; }
    .slide-delay-4 { animation-delay: 0.4s; }
    
    /* Hover Effects */
    .hover-scale {
        transition: all 0.3s ease;
    }
    
    .hover-scale:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    /* Status circles with pulse animation */
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    
    .pulse-green {
        animation: pulse 2s infinite;
    }
    
    .qr-container {
        transition: all 0.3s ease;
    }
    
    .qr-container:hover {
        transform: scale(1.05);
    }
</style>
@endpush

@section('content')
<div id="loading-overlay" class="fixed inset-0 flex items-center justify-center z-50 bg-white bg-opacity-90">
    <div class="text-center">
        <div class="loading-spinner mx-auto mb-4"></div>
        <p class="text-gray-600">Memuat detail booking...</p>
    </div>
</div>

<div class="opacity-0" id="main-content">
    <!-- Back button and action buttons -->
    <div class="flex flex-col sm:flex-row justify-between mb-6">
        <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-md mb-2 sm:mb-0">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
        
        <div class="flex space-x-2">
            @if($booking->status === 'pending')
                <button type="button" onclick="openStatusModal('confirmed')" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Konfirmasi
                </button>
                <button type="button" onclick="openStatusModal('cancelled')" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Batalkan
                </button>
            @elseif($booking->status === 'confirmed')
                <button type="button" onclick="openStatusModal('completed')" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Selesaikan
                </button>
                <button type="button" onclick="openStatusModal('cancelled')" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Batalkan
                </button>
            @endif
            
            @if($booking->status === 'confirmed' && !$booking->qr_code)
                <form action="{{ route('admin.bookings.generate-qrcode', $booking->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                        Generate QR Code
                    </button>
                </form>
            @endif
        </div>
    </div>
    
    <!-- Booking Status Timeline -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6 opacity-0 hover-scale slide-in-up slide-delay-1">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Status Booking</h3>
        <div class="relative">
            <div class="absolute left-5 top-0 h-full border-l-2 border-gray-200"></div>
            
            <div class="flex items-start mb-6 relative">
                <div class="absolute left-0 mt-1.5 w-10 h-10 rounded-full flex items-center justify-center {{ $booking->status == 'pending' || $booking->status == 'confirmed' || $booking->status == 'completed' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-14">
                    <h4 class="text-lg font-semibold text-gray-900">Pending</h4>
                    <p class="text-sm text-gray-600">{{ $booking->created_at->format('d M Y H:i') }}</p>
                    <p class="text-sm text-gray-800 mt-1">Booking telah dibuat dan menunggu konfirmasi.</p>
                </div>
            </div>
            
            <div class="flex items-start mb-6 relative">
                <div class="absolute left-0 mt-1.5 w-10 h-10 rounded-full flex items-center justify-center {{ $booking->status == 'confirmed' || $booking->status == 'completed' ? 'bg-green-500 text-white pulse-green' : ($booking->status == 'cancelled' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-500') }}">
                    @if($booking->status == 'cancelled')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @endif
                </div>
                <div class="ml-14">
                    @if($booking->status == 'cancelled')
                        <h4 class="text-lg font-semibold text-red-600">Dibatalkan</h4>
                        <p class="text-sm text-gray-600">{{ $booking->updated_at->format('d M Y H:i') }}</p>
                        <p class="text-sm text-gray-800 mt-1">Booking telah dibatalkan.</p>
                    @else
                        <h4 class="text-lg font-semibold {{ $booking->status == 'confirmed' || $booking->status == 'completed' ? 'text-green-600' : 'text-gray-400' }}">Confirmed</h4>
                        <p class="text-sm {{ $booking->status == 'confirmed' || $booking->status == 'completed' ? 'text-gray-600' : 'text-gray-400' }}">
                            {{ $booking->status == 'confirmed' || $booking->status == 'completed' ? $booking->updated_at->format('d M Y H:i') : 'Menunggu konfirmasi' }}
                        </p>
                        <p class="text-sm {{ $booking->status == 'confirmed' || $booking->status == 'completed' ? 'text-gray-800' : 'text-gray-400' }} mt-1">
                            {{ $booking->status == 'confirmed' || $booking->status == 'completed' ? 'Booking telah dikonfirmasi dan tiket telah disiapkan.' : 'Booking belum dikonfirmasi.' }}
                        </p>
                    @endif
                </div>
            </div>
            
            @if($booking->status != 'cancelled')
                <div class="flex items-start relative">
                    <div class="absolute left-0 mt-1.5 w-10 h-10 rounded-full flex items-center justify-center {{ $booking->status == 'completed' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-500' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-14">
                        <h4 class="text-lg font-semibold {{ $booking->status == 'completed' ? 'text-blue-600' : 'text-gray-400' }}">Completed</h4>
                        <p class="text-sm {{ $booking->status == 'completed' ? 'text-gray-600' : 'text-gray-400' }}">
                            {{ $booking->status == 'completed' ? $booking->updated_at->format('d M Y H:i') : 'Menunggu penyelesaian' }}
                        </p>
                        <p class="text-sm {{ $booking->status == 'completed' ? 'text-gray-800' : 'text-gray-400' }} mt-1">
                            {{ $booking->status == 'completed' ? 'Pengunjung telah menyelesaikan kunjungan.' : 'Pengunjung belum menyelesaikan kunjungan.' }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Booking Info -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6 opacity-0 hover-scale slide-in-up slide-delay-2">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Booking</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="mb-4">
                            <h4 class="text-sm text-gray-500 uppercase">ID Booking</h4>
                            <p class="text-md font-semibold">#{{ $booking->id }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <h4 class="text-sm text-gray-500 uppercase">Tanggal Kunjungan</h4>
                            <p class="text-md font-semibold">{{ $booking->visit_date->format('d M Y') }}</p>
                            <p class="text-sm text-gray-500">{{ $booking->visit_date->format('H:i') }} WIB</p>
                        </div>
                        
                        <div class="mb-4">
                            <h4 class="text-sm text-gray-500 uppercase">Jumlah Tiket</h4>
                            <p class="text-md font-semibold">{{ $booking->number_of_tickets }} tiket</p>
                        </div>
                        
                        <div class="mb-4">
                            <h4 class="text-sm text-gray-500 uppercase">Total Harga</h4>
                            <p class="text-md font-semibold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    
                    <div>
                        <div class="mb-4">
                            <h4 class="text-sm text-gray-500 uppercase">Tanggal Booking</h4>
                            <p class="text-md font-semibold">{{ $booking->created_at->format('d M Y') }}</p>
                            <p class="text-sm text-gray-500">{{ $booking->created_at->format('H:i') }} WIB</p>
                        </div>
                        
                        <div class="mb-4">
                            <h4 class="text-sm text-gray-500 uppercase">Metode Pembayaran</h4>
                            <p class="text-md font-semibold">
                                @if($booking->payment_method)
                                    {{ ucfirst($booking->payment_method) }}
                                @else
                                    E-Wallet
                                @endif
                            </p>
                        </div>
                        
                        <div class="mb-4">
                            <h4 class="text-sm text-gray-500 uppercase">Status Pembayaran</h4>
                            <p class="text-md font-semibold">
                                @if($booking->payment_status)
                                    {{ ucfirst($booking->payment_status) }}
                                @else
                                    {{ $booking->status == 'pending' ? 'Belum Dibayar' : 'Dibayar' }}
                                @endif
                            </p>
                        </div>
                        
                        <div class="mb-4">
                            <h4 class="text-sm text-gray-500 uppercase">Catatan</h4>
                            <p class="text-md font-semibold">
                                @if($booking->notes)
                                    {{ $booking->notes }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Destination Info -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6 opacity-0 hover-scale slide-in-up slide-delay-3">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Destinasi</h3>
                
                @if($booking->destination)
                    <div class="flex flex-col md:flex-row">
                        <div class="flex-shrink-0 w-full md:w-1/3 mb-4 md:mb-0 md:mr-6">
                            @if($booking->destination->image)
                                <img class="w-full h-48 object-cover rounded-lg" src="{{ Storage::url($booking->destination->image) }}" alt="{{ $booking->destination->name }}">
                            @else
                                <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex-grow">
                            <h4 class="text-xl font-semibold text-gray-900 mb-2">{{ $booking->destination->name }}</h4>
                            <p class="text-sm text-gray-500 mb-3">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $booking->destination->location }}
                            </p>
                            
                            <div class="grid grid-cols-2 gap-4 mb-3">
                                <div>
                                    <h5 class="text-sm text-gray-500">Harga Tiket</h5>
                                    <p class="font-medium">Rp {{ number_format($booking->destination->price, 0, ',', '.') }}</p>
                                </div>
                                
                                <div>
                                    <h5 class="text-sm text-gray-500">Status Destinasi</h5>
                                    <p class="font-medium">{{ ucfirst($booking->destination->status) }}</p>
                                </div>
                            </div>
                            
                            <p class="text-sm text-gray-700 line-clamp-3">{{ $booking->destination->description }}</p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-6 bg-gray-50 rounded-lg">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-500">Informasi destinasi tidak tersedia atau ini adalah paket wisata.</p>
                    </div>
                @endif
            </div>
            
            <!-- User Info -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6 opacity-0 hover-scale slide-in-up slide-delay-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pengguna</h3>
                
                @if($booking->user)
                    <div class="flex items-center mb-4">
                        @if($booking->user->profile_picture)
                            <img class="h-16 w-16 rounded-full object-cover" src="{{ Storage::url($booking->user->profile_picture) }}" alt="{{ $booking->user->name }}">
                        @else
                            <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-xl font-medium text-indigo-600">{{ substr($booking->user->name, 0, 1) }}</span>
                            </div>
                        @endif
                        
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900">{{ $booking->user->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $booking->user->email }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h5 class="text-sm text-gray-500 uppercase">Nomor Telepon</h5>
                            <p class="font-medium">{{ $booking->user->phone_number ?? 'Tidak tersedia' }}</p>
                        </div>
                        
                        <div>
                            <h5 class="text-sm text-gray-500 uppercase">Bergabung Sejak</h5>
                            <p class="font-medium">{{ $booking->user->created_at->format('d M Y') }}</p>
                        </div>
                        
                        <div>
                            <h5 class="text-sm text-gray-500 uppercase">Total Booking</h5>
                            <p class="font-medium">{{ $booking->user->bookings()->count() }} booking</p>
                        </div>
                        
                        <div>
                            <h5 class="text-sm text-gray-500 uppercase">Alamat</h5>
                            <p class="font-medium">{{ $booking->user->address ?? 'Tidak tersedia' }}</p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-6 bg-gray-50 rounded-lg">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <p class="text-gray-500">Informasi pengguna tidak tersedia.</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- QR Code and Actions -->
        <div>
            @if($booking->status == 'confirmed' || $booking->status == 'completed')
                <div class="bg-white rounded-lg shadow-md p-6 mb-6 opacity-0 hover-scale slide-in-up slide-delay-2">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Tiket Digital</h3>
                    
                    @if($qrCodeUrl)
                        <div class="flex flex-col items-center qr-container">
                            <img src="{{ $qrCodeUrl }}" alt="QR Code" class="w-48 h-48 mb-3">
                            <p class="text-sm text-gray-500">QR Code untuk verifikasi tiket</p>
                            <a href="{{ $qrCodeUrl }}" download="ticket_{{ $booking->id }}.png" class="mt-3 inline-flex items-center px-4 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 text-sm font-medium rounded-md transition duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Download QR
                            </a>
                        </div>
                    @else
                        <div class="text-center py-6 bg-gray-50 rounded-lg">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                            <p class="text-gray-500 mb-3">QR Code belum dibuat</p>
                            <form action="{{ route('admin.bookings.generate-qrcode', $booking->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                    </svg>
                                    Generate QR Code
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endif
            
            <!-- Additional Actions -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6 opacity-0 hover-scale slide-in-up slide-delay-3">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Tindakan</h3>
                
                <div class="space-y-3">
                    <a href="{{ route('admin.bookings.index') }}" class="flex items-center px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-md transition-colors">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        Lihat Semua Booking
                    </a>
                    
                    @if($booking->user)
                    <a href="{{ route('admin.users.show', $booking->user->id) }}" class="flex items-center px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-md transition-colors">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Lihat Profil User
                    </a>
                    @endif
                    
                    @if($booking->destination)
                    <a href="{{ route('admin.destinations.show', $booking->destination->id) }}" class="flex items-center px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-md transition-colors">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Lihat Destinasi
                    </a>
                    @endif
                    
                    <button type="button" onclick="openStatusModal('{{ $booking->status }}')" class="flex items-center px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md transition-colors w-full text-left">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Update Status
                    </button>
                    
                    @if($booking->qr_code)
                    <a href="{{ $qrCodeUrl }}" download="ticket_{{ $booking->id }}.png" class="flex items-center px-4 py-2 bg-green-50 hover:bg-green-100 text-green-700 rounded-md transition-colors">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download QR Code
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div id="status-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.bookings.update-status', $booking->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" id="status-input" value="">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10" id="modal-icon-container">
                            <svg class="h-6 w-6 text-blue-600" id="modal-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Update Status
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" id="modal-description">
                                    Apakah Anda yakin ingin mengupdate status booking ini?
                                </p>
                                <div class="mt-4">
                                    <label for="notification_message" class="block text-sm font-medium text-gray-700">Pesan Notifikasi</label>
                                    <textarea id="notification_message" name="notification_message" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Pesan untuk user (opsional)"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" id="confirm-button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Konfirmasi
                    </button>
                    <button type="button" onclick="closeStatusModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simulation loading
        setTimeout(function() {
            document.getElementById('loading-overlay').style.display = 'none';
            document.getElementById('main-content').classList.add('fade-in');
        }, 500);
    });
    
    // Status modal functions
    function openStatusModal(status) {
        const modal = document.getElementById('status-modal');
        const statusInput = document.getElementById('status-input');
        const modalTitle = document.getElementById('modal-title');
        const modalDescription = document.getElementById('modal-description');
        const confirmButton = document.getElementById('confirm-button');
        const iconContainer = document.getElementById('modal-icon-container');
        const icon = document.getElementById('modal-icon');
        
        // Set the status value
        statusInput.value = status;
        
        // Update modal content based on status
        if (status === 'confirmed') {
            modalTitle.textContent = 'Konfirmasi Booking';
            modalDescription.textContent = 'Apakah Anda yakin ingin mengkonfirmasi booking ini? User akan menerima notifikasi dan QR code akan dibuat.';
            confirmButton.textContent = 'Konfirmasi';
            confirmButton.classList.remove('bg-red-600', 'hover:bg-red-700', 'focus:ring-red-500', 'bg-blue-600', 'hover:bg-blue-700', 'focus:ring-blue-500');
            confirmButton.classList.add('bg-green-600', 'hover:bg-green-700', 'focus:ring-green-500');
            iconContainer.classList.remove('bg-red-100', 'bg-blue-100');
            iconContainer.classList.add('bg-green-100');
            icon.classList.remove('text-red-600', 'text-blue-600');
            icon.classList.add('text-green-600');
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
        } else if (status === 'cancelled') {
            modalTitle.textContent = 'Batalkan Booking';
            modalDescription.textContent = 'Apakah Anda yakin ingin membatalkan booking ini? Tindakan ini tidak dapat dibatalkan.';
            confirmButton.textContent = 'Ya, Batalkan';
            confirmButton.classList.remove('bg-green-600', 'hover:bg-green-700', 'focus:ring-green-500', 'bg-blue-600', 'hover:bg-blue-700', 'focus:ring-blue-500');
            confirmButton.classList.add('bg-red-600', 'hover:bg-red-700', 'focus:ring-red-500');
            iconContainer.classList.remove('bg-green-100', 'bg-blue-100');
            iconContainer.classList.add('bg-red-100');
            icon.classList.remove('text-green-600', 'text-blue-600');
            icon.classList.add('text-red-600');
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
        } else if (status === 'completed') {
            modalTitle.textContent = 'Selesaikan Booking';
            modalDescription.textContent = 'Apakah Anda yakin ingin menandai booking ini sebagai selesai?';
            confirmButton.textContent = 'Ya, Selesaikan';
            confirmButton.classList.remove('bg-green-600', 'hover:bg-green-700', 'focus:ring-green-500', 'bg-red-600', 'hover:bg-red-700', 'focus:ring-red-500');
            confirmButton.classList.add('bg-blue-600', 'hover:bg-blue-700', 'focus:ring-blue-500');
            iconContainer.classList.remove('bg-green-100', 'bg-red-100');
            iconContainer.classList.add('bg-blue-100');
            icon.classList.remove('text-green-600', 'text-red-600');
            icon.classList.add('text-blue-600');
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
        } else {
            modalTitle.textContent = 'Update Status';
            modalDescription.textContent = 'Pilih status baru untuk booking ini.';
            confirmButton.textContent = 'Update';
            confirmButton.classList.remove('bg-green-600', 'hover:bg-green-700', 'focus:ring-green-500', 'bg-red-600', 'hover:bg-red-700', 'focus:ring-red-500');
            confirmButton.classList.add('bg-blue-600', 'hover:bg-blue-700', 'focus:ring-blue-500');
            iconContainer.classList.remove('bg-green-100', 'bg-red-100');
            iconContainer.classList.add('bg-blue-100');
            icon.classList.remove('text-green-600', 'text-red-600');
            icon.classList.add('text-blue-600');
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>';
        }
        
        // Show the modal
        modal.classList.remove('hidden');
    }
    
    function closeStatusModal() {
        const modal = document.getElementById('status-modal');
        modal.classList.add('hidden');
    }
</script>
@endpush