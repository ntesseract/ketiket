@extends('layouts.app')

@section('title', $destination->name)

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Animation */
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
    
    .fade-in {
        animation: fadeIn 0.6s ease-in-out forwards;
    }
    
    .slide-in-up {
        animation: slideInUp 0.6s ease-in-out forwards;
    }
    
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    .delay-400 { animation-delay: 0.4s; }
    
    /* Hover effects */
    .hover-scale {
        transition: all 0.3s ease;
    }
    
    .hover-scale:hover {
        transform: scale(1.03);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    /* Star Rating */
    .stars-container {
        display: inline-flex;
        gap: 0.25rem;
    }
    
    .star-btn {
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        transition: transform 0.2s;
    }
    
    .star-btn:hover {
        transform: scale(1.2);
    }
    
    /* Scroll Spy */
    .scroll-spy-active {
        color: #4f46e5; /* Indigo-600 */
        font-weight: 600;
        border-bottom: 2px solid #4f46e5;
    }
    
    /* Media Queries for Responsiveness */
    @media (max-width: 768px) {
        .scroll-nav {
            overflow-x: auto;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 0.5rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Header Section with Main Image -->
<div class="relative">
    <div class="w-full h-80 md:h-96 bg-gray-300 relative">
        @if($destination->image)
            <img src="{{ Storage::url($destination->image) }}" alt="{{ $destination->name }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                <svg class="h-24 w-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent opacity-70"></div>
    </div>
    
    <div class="absolute bottom-0 inset-x-0 p-6 text-white">
        <div class="container mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold mb-2 text-shadow">{{ $destination->name }}</h1>
                    <div class="flex flex-wrap items-center mb-1 text-white/90">
                        <span class="flex items-center mr-4">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ $destination->location }}
                        </span>
                        
                        <span class="flex items-center mr-4">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                            {{ number_format($averageRating, 1) }} ({{ $reviews->count() }} ulasan)
                        </span>
                        
                        <span class="flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            @if($destination->opening_hour && $destination->closing_hour)
                                {{ \Carbon\Carbon::parse($destination->opening_hour)->format('H:i') }} - {{ \Carbon\Carbon::parse($destination->closing_hour)->format('H:i') }}
                            @else
                                Jam operasional tidak tersedia
                            @endif
                        </span>
                    </div>
                
                    <div class="inline-block mt-2 px-3 py-1 rounded-full 
                        {{ $destination->status === 'open' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $destination->status === 'closed' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $destination->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                        <span class="flex items-center text-sm font-medium">
                            <span class="h-2 w-2 mr-1.5 rounded-full 
                                {{ $destination->status === 'open' ? 'bg-green-600' : '' }}
                                {{ $destination->status === 'closed' ? 'bg-red-600' : '' }}
                                {{ $destination->status === 'maintenance' ? 'bg-yellow-600' : '' }}"></span>
                            {{ ucfirst($destination->status) }}
                        </span>
                    </div>
                </div>
                
                <div class="mt-4 md:mt-0 flex space-x-2">
                    @auth
                        <form action="{{ route('destinations.toggleFavorite', $destination) }}" method="POST" class="mr-2">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 backdrop-blur-sm rounded-md transition-colors duration-300">
                                @if($isFavorite)
                                    <svg class="w-5 h-5 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-white">Favorit</span>
                                @else
                                    <svg class="w-5 h-5 text-white mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                    <span class="text-white">Tambah ke Favorit</span>
                                @endif
                            </button>
                        </form>
                    @endauth
                    
                    <a href="#booking" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md transition-colors duration-300">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Booking Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sticky Navigation -->
<div class="sticky top-0 z-20 bg-white shadow-md" id="scroll-spy-nav">
    <div class="container mx-auto px-4">
        <div class="scroll-nav flex space-x-6 py-4 overflow-x-auto">
            <a href="#overview" class="text-gray-600 hover:text-indigo-600 py-2 scroll-spy-link">Overview</a>
            <a href="#location" class="text-gray-600 hover:text-indigo-600 py-2 scroll-spy-link">Lokasi</a>
            <a href="#reviews" class="text-gray-600 hover:text-indigo-600 py-2 scroll-spy-link">Ulasan</a>
            <a href="#nearby" class="text-gray-600 hover:text-indigo-600 py-2 scroll-spy-link">Tempat Terdekat</a>
            <a href="#booking" class="text-gray-600 hover:text-indigo-600 py-2 scroll-spy-link">Booking</a>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content (Left Column) -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Overview Section -->
            <section id="overview" class="bg-white rounded-lg shadow-md p-6 opacity-0 hover-scale" data-animate="fade-in">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Tentang Destinasi</h2>
                <div class="prose max-w-none text-gray-600">
                    {{ $destination->description }}
                </div>
                
                <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <svg class="h-8 w-8 text-indigo-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-sm text-gray-500">Harga Tiket</div>
                        <div class="font-bold text-gray-800">Rp {{ number_format($destination->price, 0, ',', '.') }}</div>
                    </div>
                    
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <svg class="h-8 w-8 text-indigo-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-sm text-gray-500">Jam Operasional</div>
                        <div class="font-bold text-gray-800">
                            @if($destination->opening_hour && $destination->closing_hour)
                                {{ \Carbon\Carbon::parse($destination->opening_hour)->format('H:i') }} - {{ \Carbon\Carbon::parse($destination->closing_hour)->format('H:i') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <svg class="h-8 w-8 text-indigo-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <div class="text-sm text-gray-500">Kapasitas</div>
                        <div class="font-bold text-gray-800">
                            @if($destination->capacity)
                                {{ number_format($destination->capacity, 0, ',', '.') }} orang
                            @else
                                Tidak terbatas
                            @endif
                        </div>
                    </div>
                    
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <svg class="h-8 w-8 text-indigo-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        <div class="text-sm text-gray-500">Rating</div>
                        <div class="font-bold text-gray-800">{{ number_format($averageRating, 1) }}/5.0</div>
                    </div>
                </div>
            </section>
            
            <!-- Location Section -->
            <section id="location" class="bg-white rounded-lg shadow-md p-6 opacity-0 hover-scale" data-animate="fade-in" data-delay="100">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Lokasi</h2>
                <div class="h-80 w-full rounded-lg overflow-hidden mb-4" id="map"></div>
                <div class="flex items-start text-gray-600">
                    <svg class="h-5 w-5 text-indigo-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <address class="not-italic">
                        {{ $destination->location }}
                    </address>
                </div>
            </section>
            
            <!-- Reviews Section -->
            <section id="reviews" class="bg-white rounded-lg shadow-md p-6 opacity-0 hover-scale" data-animate="fade-in" data-delay="200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Ulasan & Rating</h2>
                    <div class="flex items-center">
                        <div class="text-3xl font-bold text-gray-800 mr-2">{{ number_format($averageRating, 1) }}</div>
                        <div class="flex text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($averageRating))
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @else
                                    <svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <span class="ml-2 text-gray-500 text-sm">({{ $reviews->count() }} ulasan)</span>
                    </div>
                </div>
                
                <!-- Rating Breakdown -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            @for($i = 5; $i >= 1; $i--)
                                @php
                                    $reviewCount = $reviews->where('rating', $i)->count();
                                    $percentage = $reviews->count() > 0 ? ($reviewCount / $reviews->count()) * 100 : 0;
                                @endphp
                                <div class="flex items-center mb-1">
                                    <span class="w-10 text-sm text-gray-600">{{ $i }} star</span>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mx-2 flex-1">
                                        <div class="bg-yellow-400 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="w-12 text-sm text-gray-600">{{ $reviewCount }}</span>
                                </div>
                            @endfor
                        </div>
                        <div class="flex items-center justify-center">
                            <a href="#write-review" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md transition-colors duration-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                                Tulis Ulasan
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Reviews List -->
                @if($reviews->count() > 0)
                    <div class="space-y-4">
                        @foreach($reviews->take(5) as $review)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-indigo-200 transition-colors duration-300">
                                <div class="flex justify-between">
                                    <div class="flex items-center">
                                        @if($review->user->profile_picture)
                                            <img src="{{ Storage::url($review->user->profile_picture) }}" alt="{{ $review->user->name }}" class="h-10 w-10 rounded-full object-cover">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span class="text-indigo-800 font-semibold">{{ strtoupper(substr($review->user->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                        <div class="ml-3">
                                            <h4 class="font-medium text-gray-800">{{ $review->user->name }}</h4>
                                            <div class="flex items-center mt-1">
                                                <div class="flex text-yellow-400">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $review->rating)
                                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                            </svg>
                                                        @else
                                                            <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                            </svg>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span class="ml-2 text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 text-gray-600">
                                    {{ $review->comment }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($reviews->count() > 5)
                        <div class="mt-6 text-center">
                            <button type="button" id="load-more-reviews" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                Lihat Semua Ulasan ({{ $reviews->count() }})
                            </button>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8 bg-gray-50 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">Belum ada ulasan</h3>
                        <p class="mt-1 text-gray-500">Jadilah yang pertama memberikan ulasan untuk destinasi ini</p>
                    </div>
                @endif
                
                <!-- Write Review Form -->
                @auth
                    <div id="write-review" class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Tulis Ulasan Anda</h3>
                        <form action="{{ route('destinations.storeReview', $destination) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                                <div class="stars-container" id="rating-container">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" class="star-btn" data-rating="{{ $i }}">
                                            <svg class="h-8 w-8 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        </button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="rating-input" value="">
                                @error('rating')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Komentar</label>
                                <textarea id="comment" name="comment" rows="4" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Bagikan pengalaman Anda mengunjungi destinasi ini..."></textarea>
                                @error('comment')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Kirim Ulasan
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                        <p class="text-gray-600 mb-4">Silakan login untuk memberikan ulasan</p>
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            Login
                        </a>
                    </div>
                @endauth
            </section>
            
            <!-- Nearby Attractions Section -->
            <section id="nearby" class="bg-white rounded-lg shadow-md p-6 opacity-0 hover-scale" data-animate="fade-in" data-delay="300">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Tempat Menarik Terdekat</h2>
                
                @if(isset($nearbyAttractions) && count($nearbyAttractions) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($nearbyAttractions as $attraction)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-indigo-200 hover:shadow-md transition-all duration-300">
                                <div class="flex items-start">
                                    @if(isset($attraction->image))
                                        <img src="{{ Storage::url($attraction->image) }}" alt="{{ $attraction->name }}" class="h-16 w-16 rounded object-cover mr-3">
                                    @else
                                        <div class="h-16 w-16 rounded bg-indigo-100 flex items-center justify-center mr-3">
                                            <svg class="h-8 w-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="font-medium text-gray-800">{{ $attraction->name }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ $attraction->location }}</p>
                                        
                                        <div class="flex items-center mt-2">
                                            <svg class="h-4 w-4 text-indigo-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-xs text-gray-500">{{ $attraction->distance }} km dari sini</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-50 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">Tidak ada tempat menarik terdekat</h3>
                        <p class="mt-1 text-gray-500">Informasi tempat menarik di sekitar destinasi ini belum tersedia</p>
                    </div>
                @endif
            </section>
            
            <!-- Booking Section -->
            <section id="booking" class="bg-white rounded-lg shadow-md p-6 opacity-0 hover-scale" data-animate="fade-in" data-delay="400">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Booking Tiket</h2>
                
                @if($destination->status === 'open')
                    <div class="mb-6 bg-indigo-50 p-4 rounded-lg border-l-4 border-indigo-500">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-indigo-700">
                                    Destinasi ini tersedia untuk booking. Silakan pilih tanggal kunjungan dan jumlah tiket di bawah.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    @auth
                        <!-- Booking Form -->
                        <form action="{{ route('booking.storeDestination', $destination) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="visit_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kunjungan <span class="text-red-500">*</span></label>
                                <input type="date" name="visit_date" id="visit_date" required min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                @error('visit_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="number_of_tickets" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Tiket <span class="text-red-500">*</span></label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="number" name="number_of_tickets" id="number_of_tickets" required min="1" value="1"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                @error('number_of_tickets')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-600">Harga per tiket:</span>
                                    <span class="font-medium">Rp {{ number_format($destination->price, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total:</span>
                                    <span class="text-indigo-600" id="total-price">Rp {{ number_format($destination->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                    Lanjutkan ke Pembayaran
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-8 bg-gray-50 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900">Login untuk melakukan booking</h3>
                            <p class="mt-1 text-gray-500">Silakan login terlebih dahulu untuk melakukan booking tiket</p>
                            <div class="mt-4">
                                <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                                    Login
                                </a>
                            </div>
                        </div>
                    @endauth
                @elseif($destination->status === 'maintenance')
                    <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-500">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Destinasi dalam Pemeliharaan</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Destinasi ini sedang dalam pemeliharaan dan tidak tersedia untuk booking saat ini. Silakan coba kembali nanti.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-500">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Destinasi Tutup</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p>Destinasi ini sedang tutup dan tidak tersedia untuk booking saat ini.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </section>
        </div>
        
        <!-- Sidebar (Right Column) -->
        <div class="space-y-6">
            <!-- Weather -->
            <div class="bg-white rounded-lg shadow-md p-6 opacity-0 hover-scale" data-animate="fade-in" data-delay="100">
                <h3 class="font-bold text-lg text-gray-800 mb-3 flex items-center">
                    <svg class="h-5 w-5 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                    </svg>
                    Cuaca Hari Ini
                </h3>
                
                <div class="flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-3xl font-bold text-gray-900">28°C</span>
                        <span class="text-sm text-gray-600">Cerah Berawan</span>
                    </div>
                    <svg class="h-14 w-14 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                
                <div class="mt-4 grid grid-cols-3 gap-2 border-t border-gray-200 pt-4">
                    <div class="text-center">
                        <div class="text-xs text-gray-500">Kelembaban</div>
                        <div class="font-semibold">70%</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-500">Angin</div>
                        <div class="font-semibold">7 km/h</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-500">Indeks UV</div>
                        <div class="font-semibold">Sedang</div>
                    </div>
                </div>
            </div>
            
            <!-- Similar Destinations -->
            <div class="bg-white rounded-lg shadow-md p-6 opacity-0 hover-scale" data-animate="fade-in" data-delay="200">
                <h3 class="font-bold text-lg text-gray-800 mb-3 flex items-center">
                    <svg class="h-5 w-5 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path>
                    </svg>
                    Destinasi Serupa
                </h3>
                
                <div class="space-y-4">
                    @foreach($similarDestinations as $similarDestination)
                        <a href="{{ route('destinations.show', $similarDestination) }}" class="block hover:bg-gray-50 rounded-lg p-3 transition duration-200">
                            <div class="flex items-start space-x-3">
                                @if($similarDestination->image)
                                    <img src="{{ Storage::url($similarDestination->image) }}" class="h-14 w-14 rounded object-cover flex-shrink-0" alt="{{ $similarDestination->name }}">
                                @else
                                    <div class="h-14 w-14 rounded bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="h-6 w-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $similarDestination->name }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $similarDestination->location }}</p>
                                    <div class="flex items-center mt-1">
                                        <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        <span class="text-xs text-gray-500 ml-1">
                                            {{ number_format($similarDestination->reviews->avg('rating') ?? 0, 1) }}
                                            ({{ $similarDestination->reviews->count() }})
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-indigo-600">Rp {{ number_format($similarDestination->price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            
            <!-- Quick Info -->
            <div class="bg-white rounded-lg shadow-md p-6 opacity-0 hover-scale" data-animate="fade-in" data-delay="300">
                <h3 class="font-bold text-lg text-gray-800 mb-3 flex items-center">
                    <svg class="h-5 w-5 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Informasi Penting
                </h3>
                
                <div class="space-y-3">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Pembayaran Online</h4>
                            <p class="text-xs text-gray-500">Tiket dapat dibayar melalui dompet elektronik atau kartu kredit</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Refund</h4>
                            <p class="text-xs text-gray-500">Dapat di-refund hingga 1 hari sebelum tanggal kunjungan</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Reschedule</h4>
                            <p class="text-xs text-gray-500">Dapat melakukan reschedule hingga 1 hari sebelum tanggal kunjungan</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Fasilitas</h4>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex items-center">
                            <svg class="h-4 w-4 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-xs text-gray-600">Spot Foto</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="h-4 w-4 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            <span class="text-xs text-gray-600">ATM</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="h-4 w-4 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                            <span class="text-xs text-gray-600">WiFi</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="h-4 w-4 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <span class="text-xs text-gray-600">Toko Souvenir</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="h-4 w-4 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <span class="text-xs text-gray-600">Medis</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="h-4 w-4 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span class="text-xs text-gray-600">Restoran</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Help & Support -->
            <div class="bg-white rounded-lg shadow-md p-6 opacity-0 hover-scale" data-animate="fade-in" data-delay="400">
                <h3 class="font-bold text-lg text-gray-800 mb-3 flex items-center">
                    <svg class="h-5 w-5 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    Bantuan & Dukungan
                </h3>
                
                <div class="space-y-3">
                    <a href="#" class="flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        FAQ Seputar Destinasi
                    </a>
                    <a href="{{ route('chat.index') }}" class="flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Chat dengan AI Assistant
                    </a>
                    <a href="#" class="flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Email Customer Support
                    </a>
                    <a href="#" class="flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        Hubungi Call Center
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map
        @if($destination->latitude && $destination->longitude)
            var map = L.map('map').setView([{{ $destination->latitude }}, {{ $destination->longitude }}], 15);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            L.marker([{{ $destination->latitude }}, {{ $destination->longitude }}])
                .addTo(map)
                .bindPopup('{{ $destination->name }}')
                .openPopup();
        @endif
        
        // Initialize lightbox for images
        const lightbox = GLightbox({
            selector: '.lightbox-image'
        });
        
        // Star rating functionality
        const starButtons = document.querySelectorAll('.star-btn');
        const ratingInput = document.getElementById('rating-input');
        
        if (starButtons.length > 0 && ratingInput) {
            starButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const rating = this.getAttribute('data-rating');
                    ratingInput.value = rating;
                    
                    // Update star colors
                    starButtons.forEach(star => {
                        const starRating = star.getAttribute('data-rating');
                        if (starRating <= rating) {
                            star.querySelector('svg').classList.remove('text-gray-300');
                            star.querySelector('svg').classList.add('text-yellow-400');
                        } else {
                            star.querySelector('svg').classList.remove('text-yellow-400');
                            star.querySelector('svg').classList.add('text-gray-300');
                        }
                    });
                });
            });
        }
        
        // Calculate total price for booking
        const numberOfTicketsInput = document.getElementById('number_of_tickets');
        const totalPriceDisplay = document.getElementById('total-price');
        
        if (numberOfTicketsInput && totalPriceDisplay) {
            const ticketPrice = {{ $destination->price }};
            
            numberOfTicketsInput.addEventListener('input', function() {
                const quantity = parseInt(this.value) || 0;
                const total = quantity * ticketPrice;
                
                // Format to Indonesian currency
                const formatter = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
                
                totalPriceDisplay.textContent = formatter.format(total).replace('IDR', 'Rp');
            });
        }
        
        // Scroll spy for navigation
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.scroll-spy-link');
        
        function activateNavByScroll() {
            const scrollPosition = window.scrollY;

            sections.forEach(section => {
                const sectionTop = section.offsetTop - 100; // Offset for sticky header
                const sectionHeight = section.offsetHeight;
                const sectionId = section.getAttribute('id');
                
                if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                    navLinks.forEach(link => {
                        link.classList.remove('scroll-spy-active');
                        if (link.getAttribute('href') === `#${sectionId}`) {
                            link.classList.add('scroll-spy-active');
                        }
                    });
                }
            });
        }
        
        window.addEventListener('scroll', activateNavByScroll);
        
        // Animate elements on scroll
        const animatedElements = document.querySelectorAll('[data-animate]');
        
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    const animation = element.getAttribute('data-animate');
                    element.classList.add(animation);
                    element.style.opacity = '1';
                    observer.unobserve(element);
                }
            });
        }, { threshold: 0.1 });
        
        animatedElements.forEach(element => {
            observer.observe(element);
        });
        
        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId !== '#') {
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 70,
                            behavior: 'smooth'
                        });
                    }
                }
            });
        });
        
        // Load more reviews
        const loadMoreButton = document.getElementById('load-more-reviews');
        if (loadMoreButton) {
            loadMoreButton.addEventListener('click', function() {
                // Here you would normally load more reviews via AJAX
                // For now, let's just redirect to a full reviews page
                window.location.href = "{{ route('destinations.reviews', $destination) }}";
            });
        }
    });
</script>
@endpush