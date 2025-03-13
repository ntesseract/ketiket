@extends('layouts.admin')

@section('title', 'Detail Destinasi')

@section('header', 'Detail Destinasi')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<style>
    /* Custom Animation */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideInUp {
        from {
            transform: translateY(40px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .fade-in {
        animation: fadeIn 0.6s ease-in-out forwards;
    }
    
    .slide-in {
        animation: slideInUp 0.6s ease-in-out forwards;
    }
    
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    .delay-400 { animation-delay: 0.4s; }
    .delay-500 { animation-delay: 0.5s; }
    
    /* Hover Effects */
    .hover-scale {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }
    
    .hover-scale:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    /* Loading Animation */
    .loading-spinner {
        border: 4px solid rgba(0, 0, 0, 0.1);
        border-radius: 50%;
        border-top: 4px solid #3498db;
        width: 40px;
        height: 40px;
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
</style>
@endpush

@section('content')
<div id="loading-overlay" class="fixed inset-0 flex items-center justify-center z-50 bg-white bg-opacity-90">
    <div class="text-center">
        <div class="loading-spinner mx-auto mb-4"></div>
        <p class="text-gray-600">Memuat data destinasi...</p>
    </div>
</div>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 opacity-0" id="main-content">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden hover-scale">
        <div class="relative">
            @if($destination->image)
            <div class="w-full h-64 bg-gray-200 relative">
                <img src="{{ Storage::url($destination->image) }}" alt="{{ $destination->name }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            </div>
            @else
            <div class="w-full h-64 bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                <svg class="h-24 w-24 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            @endif
            
            <div class="absolute bottom-4 left-6 text-white">
                <h1 class="text-3xl font-bold slide-in">{{ $destination->name }}</h1>
                <p class="text-white/90 slide-in delay-100">
                    <svg class="h-5 w-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    {{ $destination->location }}
                </p>
            </div>
            
            <div class="absolute top-4 right-4 flex space-x-2">
                <a href="{{ route('admin.destinations.edit', $destination) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded-md shadow-lg transition-all duration-200 hover:scale-105">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </a>
                <form method="POST" action="{{ route('admin.destinations.destroy', $destination) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus destinasi ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-md shadow-lg transition-all duration-200 hover:scale-105">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Status Badge -->
        <div class="px-6 py-3 border-b border-gray-200 flex justify-between items-center">
            <div>
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                    {{ $destination->status === 'open' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $destination->status === 'closed' ? 'bg-red-100 text-red-800' : '' }}
                    {{ $destination->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                    {{ $destination->status === 'open' ? 'Buka' : '' }}
                    {{ $destination->status === 'closed' ? 'Tutup' : '' }}
                    {{ $destination->status === 'maintenance' ? 'Pemeliharaan' : '' }}
                </span>
                @if($destination->opening_hour && $destination->closing_hour)
                <span class="ml-3 text-sm text-gray-500">
                    <svg class="h-4 w-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ \Carbon\Carbon::parse($destination->opening_hour)->format('H:i') }} - {{ \Carbon\Carbon::parse($destination->closing_hour)->format('H:i') }}
                </span>
                @endif
            </div>
            <div class="text-lg font-semibold text-gray-700">
                Rp {{ number_format($destination->price, 0, ',', '.') }}
                <span class="text-sm font-normal text-gray-500">/orang</span>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="bg-white rounded-lg shadow-md p-6 hover-scale opacity-0" data-animate="fade-in">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Deskripsi
                </h2>
                <div class="prose max-w-none text-gray-600">
                    {{ $destination->description }}
                </div>
            </div>
            
            <!-- Map -->
            <div class="bg-white rounded-lg shadow-md p-6 hover-scale opacity-0" data-animate="fade-in" data-delay="200">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                    Lokasi di Peta
                </h2>
                @if($destination->latitude && $destination->longitude)
                <div id="map" class="h-80 w-full rounded-md border border-gray-200 mb-2"></div>
                <p class="text-sm text-gray-500">
                    <svg class="h-4 w-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Koordinat: {{ $destination->latitude }}, {{ $destination->longitude }}
                </p>
                @else
                <div class="bg-gray-100 rounded-md p-4 text-center">
                    <svg class="h-12 w-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                    <p class="text-gray-500">Koordinat tidak tersedia untuk destinasi ini.</p>
                </div>
                @endif
            </div>
            
            <!-- Bookings -->
            <div class="bg-white rounded-lg shadow-md p-6 hover-scale opacity-0" data-animate="fade-in" data-delay="400">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Booking Terbaru
                </h2>
                
                @if($destination->bookings && $destination->bookings->count() > 0)
                <div class="overflow-x-auto rounded-md">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID Booking
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal Kunjungan
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tiket
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($destination->bookings->take(5) as $booking)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $booking->booking_code }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $booking->user->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($booking->visit_date)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $booking->number_of_tickets }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $booking->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $booking->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($destination->bookings->count() > 5)
                <div class="mt-4 text-right">
                    <a href="{{ route('admin.bookings.index', ['destination_id' => $destination->id]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                        Lihat semua booking →
                    </a>
                </div>
                @endif
                @else
                <div class="py-4 text-center text-gray-500">
                    <svg class="h-12 w-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p>Belum ada booking untuk destinasi ini.</p>
                </div>
                @endif
            </div>
            
            <!-- Reviews -->
            <div class="bg-white rounded-lg shadow-md p-6 hover-scale opacity-0" data-animate="fade-in" data-delay="600">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg class="h-5 w-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        Ulasan & Rating
                    </h2>
                    <a href="{{ route('admin.destinations.reviews', $destination) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                        Lihat semua ulasan →
                    </a>
                </div>
                
                @if($destination->reviews && $destination->reviews->count() > 0)
                <div class="mb-6 flex items-center">
                    <div class="mr-4">
                        <div class="text-5xl font-bold text-gray-800">{{ number_format($averageRating, 1) }}</div>
                        <div class="flex items-center mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($averageRating))
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @else
                                    <svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <div class="text-sm text-gray-500 mt-1">{{ $totalReviews }} ulasan</div>
                    </div>
                    <div class="flex-1">
                        @for($i = 5; $i >= 1; $i--)
                            @php
                                $reviewCount = $destination->reviews->where('rating', $i)->count();
                                $percentage = $totalReviews > 0 ? ($reviewCount / $totalReviews) * 100 : 0;
                            @endphp
                            <div class="flex items-center text-sm">
                                <span class="w-3">{{ $i }}</span>
                                <svg class="h-4 w-4 text-yellow-400 ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <div class="flex-1 h-2 mx-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-yellow-400 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="text-gray-500">{{ $reviewCount }}</span>
                            </div>
                        @endfor
                    </div>
                </div>
                
                <div class="space-y-4">
                    @foreach($destination->reviews->take(3) as $review)
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <div class="flex justify-between">
                            <div class="flex items-center">
                                <div class="font-medium text-gray-800">{{ $review->user->name }}</div>
                                <span class="text-xs text-gray-500 ml-2">{{ $review->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="flex">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @else
                                        <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                        </div>
                        <div class="mt-2 text-sm text-gray-600">{{ $review->comment }}</div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="py-4 text-center text-gray-500">
                    <svg class="h-12 w-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                    <p>Belum ada ulasan untuk destinasi ini.</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Right Column - Stats -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow-md p-6 hover-scale opacity-0" data-animate="fade-in" data-delay="100">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Statistik Cepat
                </h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                        <span class="text-gray-600">Total Booking</span>
                        <span class="text-xl font-semibold">{{ $totalBookings }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                        <span class="text-gray-600">Total Pengunjung</span>
                        <span class="text-xl font-semibold">{{ $totalVisitors }}</span>
                    </div>
                    @if($destination->capacity)
                    <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                        <span class="text-gray-600">Kapasitas</span>
                        <span class="text-xl font-semibold">{{ $destination->capacity }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                        <span class="text-gray-600">Total Pendapatan</span>
                        <span class="text-xl font-semibold">Rp {{ number_format($totalVisitors * $destination->price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Other Details -->
            <div class="bg-white rounded-lg shadow-md p-6 hover-scale opacity-0" data-animate="fade-in" data-delay="300">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Informasi Tambahan
                </h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                        <span class="text-gray-600">Tanggal Dibuat</span>
                        <span class="text-gray-800">{{ $destination->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                        <span class="text-gray-600">Terakhir Diupdate</span>
                        <span class="text-gray-800">{{ $destination->updated_at->format('d M Y') }}</span>
                    </div>
                    @if($destination->opening_hour && $destination->closing_hour)
                    <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                        <span class="text-gray-600">Jam Operasional</span>
                        <span class="text-gray-800">{{ \Carbon\Carbon::parse($destination->opening_hour)->format('H:i') }} - {{ \Carbon\Carbon::parse($destination->closing_hour)->format('H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-md p-6 hover-scale opacity-0" data-animate="fade-in" data-delay="500">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tindakan
                </h2>
                
                <div class="space-y-2">
                    <a href="{{ route('admin.destinations.edit', $destination) }}" class="block w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-center transition-colors duration-200">
                        <svg class="h-5 w-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Destinasi
                    </a>
                    <a href="{{ route('admin.destinations.reviews', $destination) }}" class="block w-full py-2 px-4 bg-green-600 hover:bg-green-700 text-white rounded-md text-center transition-colors duration-200">
                        <svg class="h-5 w-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        Kelola Ulasan
                    </a>
                    <a href="{{ route('admin.bookings.index', ['destination_id' => $destination->id]) }}" class="block w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-center transition-colors duration-200">
                        <svg class="h-5 w-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Lihat Booking
                    </a>
                    <form method="POST" action="{{ route('admin.destinations.destroy', $destination) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus destinasi ini? Tindakan ini tidak dapat dibatalkan.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="block w-full py-2 px-4 bg-red-600 hover:bg-red-700 text-white rounded-md text-center transition-colors duration-200">
                            <svg class="h-5 w-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus Destinasi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Loading overlay
        setTimeout(function() {
            document.getElementById('loading-overlay').style.display = 'none';
            document.getElementById('main-content').style.opacity = '1';
            
            // Animate elements with fade-in
            const fadeElements = document.querySelectorAll('[data-animate="fade-in"]');
            fadeElements.forEach((element) => {
                setTimeout(() => {
                    element.classList.add('fade-in');
                }, element.getAttribute('data-delay') || 0);
            });
        }, 800);
        
        // Initialize map if coordinates exist
        @if($destination->latitude && $destination->longitude)
        const map = L.map('map').setView([{{ $destination->latitude }}, {{ $destination->longitude }}], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        L.marker([{{ $destination->latitude }}, {{ $destination->longitude }}])
            .addTo(map)
            .bindPopup('{{ $destination->name }}')
            .openPopup();
        @endif
        
        // Parallax Effect
        window.addEventListener('scroll', function() {
            const scrollPosition = window.pageYOffset;
            const parallaxElements = document.querySelectorAll('.parallax');
            
            parallaxElements.forEach(element => {
                const speed = element.getAttribute('data-speed') || 0.5;
                element.style.transform = `translateY(${scrollPosition * speed}px)`;
            });
        });
    });
</script>
@endpush