@extends('layouts.app')

@section('title', $hotel->name . ' - Detail Hotel')

@push('styles')
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
</style>
@endpush

@section('content')
<div class="relative">
    <!-- Hotel Hero Section -->
    <div class="w-full h-80 md:h-96 bg-gray-800 overflow-hidden relative">
        @if($hotel->cover_image)
            <img src="{{ asset('storage/' . $hotel->cover_image) }}" alt="{{ $hotel->name }}" class="w-full h-full object-cover opacity-70">
        @else
            <div class="w-full h-full bg-gradient-to-r from-blue-500 to-indigo-600"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent opacity-70"></div>
        
        <div class="absolute bottom-0 inset-x-0 p-6 text-white">
            <div class="container mx-auto">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold mb-2 text-shadow">{{ $hotel->name }}</h1>
                        <div class="flex items-center mb-2 text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $hotel->star_rating)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <div class="flex flex-wrap items-center mb-1 text-white/90">
                            <span class="flex items-center mr-4">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $hotel->location }}
                            </span>
                            
                            <span class="flex items-center mr-4">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                                {{ number_format($averageRating, 1) }} ({{ $reviews->count() }} ulasan)
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-4 md:mt-0 flex space-x-2">
                        <a href="{{ route('chat.index') }}" class="inline-flex items-center px-3 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 backdrop-blur-sm rounded-md transition-colors duration-300">
                            <svg class="w-5 h-5 text-white mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <span class="text-white">Tanya AI</span>
                        </a>
                        
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
                <a href="#overview" class="text-gray-600 hover:text-indigo-600 py-2 scroll-spy-link scroll-spy-active">Overview</a>
                <a href="#facilities" class="text-gray-600 hover:text-indigo-600 py-2 scroll-spy-link">Fasilitas</a>
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
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Tentang Hotel</h2>
                    <div class="prose max-w-none text-gray-600">
                        {{ $hotel->description }}
                    </div>
                    
                    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <svg class="h-8 w-8 text-indigo-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm text-gray-500">Harga Per Malam</div>
                            <div class="font-bold text-gray-800">Rp {{ number_format($hotel->price_per_night, 0, ',', '.') }}</div>
                        </div>
                        
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <svg class="h-8 w-8 text-indigo-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm text-gray-500">Check-in/out</div>
                            <div class="font-bold text-gray-800">14:00 / 12:00</div>
                        </div>
                        
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <svg class="h-8 w-8 text-indigo-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                            </svg>
                            <div class="text-sm text-gray-500">Rating Bintang</div>
                            <div class="font-bold text-gray-800">{{ $hotel->star_rating }} Bintang</div>
                        </div>
                        
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <svg class="h-8 w-8 text-indigo-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                            <div class="text-sm text-gray-500">Rating Pengguna</div>
                            <div class="font-bold text-gray-800">{{ number_format($averageRating, 1) }}/5.0</div>
                        </div>
                    </div>
                </section>
                
                <!-- Facilities Section -->
                <section id="facilities" class="bg-white rounded-lg shadow-md p-6 opacity-0 hover-scale" data-animate="fade-in" data-delay="100">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Fasilitas</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @if(isset($hotel->facilities) && is_array($hotel->facilities))
                            @foreach($hotel->facilities as $facility)
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>{{ $facility }}</span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-500 col-span-full">Data fasilitas tidak tersedia.</p>
                        @endif
                    </div>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-indigo-50 p-4 rounded-lg border-l-4 border-indigo-500">
                            <h3 class="font-semibold text-indigo-800 mb-2">Fasilitas Kamar</h3>
                            <ul class="space-y-2 text-sm text-gray-700">
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    AC
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    TV Layar Datar
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Minibar
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Wifi Gratis
                                </li>
                            </ul>
                        </div>
                        
                        <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                            <h3 class="font-semibold text-blue-800 mb-2">Fasilitas Hotel</h3>
                            <ul class="space-y-2 text-sm text-gray-700">
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Kolam Renang
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Restoran
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Gym
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Spa
                                </li>
                            </ul>
                        </div>
                        
                        <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                            <h3 class="font-semibold text-green-800 mb-2">Layanan</h3>
                            <ul class="space-y-2 text-sm text-gray-700">
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Room Service 24 Jam
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Laundry
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Resepsionis 24 Jam
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Penjemputan Bandara
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>
                
                <!-- Location Section -->
                <section id="location" class="bg-white rounded-lg shadow-md p-6 opacity-0 hover-scale" data-animate="fade-in" data-delay="200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Lokasi</h2>
                    @if($mapUrl)
                        <div class="w-full h-80 bg-gray-200 rounded-lg overflow-hidden mb-4">
                            <img src="{{ $mapUrl }}" alt="Map location of {{ $hotel->name }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div id="map" class="w-full h-80 bg-gray-200 rounded-lg mb-4"></div>
                    @endif
                    <div class="flex items-start text-gray-600">
                        <svg class="h-5 w-5 text-indigo-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <address class="not-italic">
                            {{ $hotel->location }}
                        </address>
                    </div>
                    
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center bg-blue-50 p-3 rounded-lg">
                            <svg class="h-10 w-10 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div>
                                <h3 class="font-semibold text-gray-800">Dari Bandara</h3>
                                <p class="text-sm text-gray-600">25 menit berkendara (15 km)</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center bg-green-50 p-3 rounded-lg">
                            <svg class="h-10 w-10 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <div>
                                <h3 class="font-semibold text-gray-800">Dari Pusat Perbelanjaan</h3>
                                <p class="text-sm text-gray-600">10 menit berjalan kaki (800 m)</p>
                            </div>
                        </div>
                    </div>
                </section>
                
                <!-- Reviews Section -->
                <section id="reviews" class="bg-white rounded-lg shadow-md p-6 opacity-0 hover-scale" data-animate="fade-in" data-delay="300">
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
                                            @if(isset($review->user->profile_picture))
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
                            <p class="mt-1 text-gray-500">Jadilah yang pertama memberikan ulasan untuk hotel ini</p>
                        </div>
                    @endif
                    
                    <!-- Write Review Form -->
                    @auth
                        <div id="write-review" class="mt-8 pt-6 border-t border-gray-200">
                            <h3 class="text-xl font-bold text-gray-800 mb-4">Tulis Ulasan Anda</h3>
                            <form action="{{ route('hotels.storeReview', $hotel->id) }}" method="POST" class="space-y-4">
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
                                    <textarea id="comment" name="comment" rows="4" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Bagikan pengalaman Anda menginap di hotel ini..."></textarea>
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
                <section id="nearby" class="bg-white rounded-lg shadow-md p-6 opacity-0 hover-scale" data-animate="fade-in" data-delay="400">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Tempat Menarik Terdekat</h2>
                    
                    @if(isset($nearbyAttractions) && count($nearbyAttractions) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($nearbyAttractions as $attraction)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-indigo-200 hover:shadow-md transition-all duration-300">
                                    <div class="flex items-start">
                                        @if(isset($attraction['image']))
                                            <img src="{{ $attraction['image'] }}" alt="{{ $attraction['name'] }}" class="h-16 w-16 rounded object-cover mr-3">
                                        @else
                                            <div class="h-16 w-16 rounded bg-indigo-100 flex items-center justify-center mr-3">
                                                <svg class="h-8 w-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="font-medium text-gray-800">{{ $attraction['name'] }}</h3>
                                            <p class="text-sm text-gray-600 mt-1">{{ $attraction['location'] ?? '' }}</p>
                                            
                                            <div class="flex items-center mt-2">
                                                <svg class="h-4 w-4 text-indigo-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="text-xs text-gray-500">{{ $attraction['distance'] }} km dari hotel</span>
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
                            <p class="mt-1 text-gray-500">Informasi tempat menarik di sekitar hotel ini belum tersedia</p>
                        </div>
                    @endif
                </section>
                
                <!-- Booking Section -->
                <section id="booking" class="bg-white rounded-lg shadow-md p-6 opacity-0 hover-scale" data-animate="fade-in" data-delay="500">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Booking Kamar</h2>
                    
                    @auth
                        <!-- Booking Form -->
                        <form action="{{ route('booking.createHotel', $hotel->id) }}" method="GET" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="check_in_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Check-in <span class="text-red-500">*</span></label>
                                    <input type="date" name="check_in_date" id="check_in_date" required min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label for="check_out_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Check-out <span class="text-red-500">*</span></label>
                                    <input type="date" name="check_out_date" id="check_out_date" required min="{{ date('Y-m-d', strtotime('+2 days')) }}"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="number_of_rooms" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kamar <span class="text-red-500">*</span></label>
                                    <select name="number_of_rooms" id="number_of_rooms" required
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        @for($i = 1; $i <= min(5, $hotel->available_rooms); $i++)
                                            <option value="{{ $i }}">{{ $i }} Kamar</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label for="number_of_guests" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Tamu <span class="text-red-500">*</span></label>
                                    <select name="number_of_guests" id="number_of_guests" required
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}">{{ $i }} Orang</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-600">Harga per kamar per malam:</span>
                                    <span class="font-medium">Rp {{ number_format($hotel->price_per_night, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total:</span>
                                    <span class="text-indigo-600" id="total-price">Rp {{ number_format($hotel->price_per_night, 0, ',', '.') }}</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-2">* Harga belum termasuk pajak dan biaya layanan.</p>
                            </div>
                            
                            <div class="flex items-center mt-2">
                                <svg class="h-5 w-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                <span class="text-sm text-gray-600">Pembayaran dapat dilakukan saat check-in atau melalui aplikasi.</span>
                            </div>
                            
                            <div>
                                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                    Lanjutkan ke Booking
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-8 bg-gray-50 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900">Login untuk melakukan booking</h3>
                            <p class="mt-1 text-gray-500">Silakan login terlebih dahulu untuk melakukan booking kamar</p>
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
                </section>
            </div>
            
            <!-- Sidebar (Right Column) -->
            <div class="space-y-6">
                <!-- Price Card -->
                <div class="bg-white rounded-lg shadow-md p-6 opacity-0 hover-scale sticky top-24" data-animate="fade-in" data-delay="100">
                    <h3 class="font-bold text-lg text-gray-800 mb-3">Harga Kamar</h3>
                    
                    <div class="mb-4 flex justify-between items-center pb-4 border-b border-gray-200">
                        <span class="text-gray-600">Harga per malam</span>
                        <span class="text-2xl font-bold text-indigo-600">Rp {{ number_format($hotel->price_per_night, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="space-y-2">
                        <a href="{{ route('booking.createHotel', $hotel->id) }}" class="block w-full text-center py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transform transition duration-300 hover:scale-105">
                            Pesan Sekarang
                        </a>
                        
                        <a href="{{ route('chat.index') }}" class="block w-full text-center py-2 border border-indigo-600 text-indigo-600 hover:bg-indigo-50 rounded-lg font-medium transition duration-300">
                            Tanya Tentang Hotel
                        </a>
                    </div>
                    
                    <div class="mt-4 space-y-3">
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Pembatalan gratis hingga 3 hari sebelum check-in
                        </div>
                        
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Pembayaran fleksibel - bayar di tempat
                        </div>
                        
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            WiFi gratis di seluruh area
                        </div>
                        
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Sarapan tersedia
                        </div>
                    </div>
                </div>
                
                <!-- Room Types -->
                <div class="bg-white rounded-lg shadow-md p-6 opacity-0 hover-scale" data-animate="fade-in" data-delay="200">
                    <h3 class="font-bold text-lg text-gray-800 mb-3">Tipe Kamar</h3>
                    
                    <div class="space-y-4">
                        <div class="p-3 border border-gray-200 rounded-lg hover:border-indigo-200 transition">
                            <div class="flex justify-between">
                                <h4 class="font-medium text-gray-800">Deluxe Double</h4>
                                <span class="text-indigo-600 font-semibold">Rp {{ number_format($hotel->price_per_night, 0, ',', '.') }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">1 tempat tidur double, AC, TV layar datar</p>
                            <div class="flex items-center mt-2 text-sm text-gray-500">
                                <svg class="h-4 w-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Tersedia {{ $hotel->available_rooms }} kamar
                            </div>
                        </div>
                        
                        <div class="p-3 border border-gray-200 rounded-lg hover:border-indigo-200 transition">
                            <div class="flex justify-between">
                                <h4 class="font-medium text-gray-800">Superior Twin</h4>
                                <span class="text-indigo-600 font-semibold">Rp {{ number_format($hotel->price_per_night * 1.2, 0, ',', '.') }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">2 tempat tidur single, AC, TV layar datar</p>
                            <div class="flex items-center mt-2 text-sm text-gray-500">
                                <svg class="h-4 w-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Tersedia 5 kamar
                            </div>
                        </div>
                        
                        <div class="p-3 border border-gray-200 rounded-lg hover:border-indigo-200 transition">
                            <div class="flex justify-between">
                                <h4 class="font-medium text-gray-800">Suite</h4>
                                <span class="text-indigo-600 font-semibold">Rp {{ number_format($hotel->price_per_night * 1.5, 0, ',', '.') }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">1 tempat tidur king, ruang tamu, bak mandi</p>
                            <div class="flex items-center mt-2 text-sm text-gray-500">
                                <svg class="h-4 w-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Tersedia 2 kamar
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Similar Hotels -->
                <div class="bg-white rounded-lg shadow-md p-6 opacity-0 hover-scale" data-animate="fade-in" data-delay="300">
                    <h3 class="font-bold text-lg text-gray-800 mb-3">Hotel Serupa</h3>
                    
                    @if($similarHotels->count() > 0)
                        <div class="space-y-4">
                            @foreach($similarHotels->take(3) as $similarHotel)
                                <a href="{{ route('hotels.show', $similarHotel->id) }}" class="block hover:bg-gray-50 rounded-lg p-3 transition duration-200">
                                    <div class="flex items-start space-x-3">
                                        @if($similarHotel->cover_image)
                                            <img src="{{ Storage::url($similarHotel->cover_image) }}" class="h-14 w-14 rounded object-cover flex-shrink-0" alt="{{ $similarHotel->name }}">
                                        @else
                                            <div class="h-14 w-14 rounded bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                                <svg class="h-6 w-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $similarHotel->name }}</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ $similarHotel->location }}</p>
                                            <div class="flex items-center mt-1">
                                                <div class="flex text-yellow-400">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $similarHotel->star_rating)
                                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                            </svg>
                                                        @else
                                                            <svg class="h-3 w-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                            </svg>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-indigo-600">Rp {{ number_format($similarHotel->price_per_night, 0, ',', '.') }}</p>
                                            <p class="text-xs text-gray-500 mt-1">per malam</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 text-center">
                            <a href="{{ route('hotels.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                Lihat semua hotel
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500">Tidak ada hotel serupa yang tersedia.</p>
                        </div>
                    @endif
                </div>
                
                <!-- Help Section -->
                <div class="bg-white rounded-lg shadow-md p-6 opacity-0 hover-scale" data-animate="fade-in" data-delay="400">
                    <h3 class="font-bold text-lg text-gray-800 mb-3">Butuh Bantuan?</h3>
                    
                    <div class="space-y-4">
                        <a href="{{ route('chat.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            <span>Chat dengan AI Assistant</span>
                        </a>
                        
                        <a href="#" class="flex items-center text-gray-600 hover:text-indigo-600">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span>Hubungi Call Center</span>
                        </a>
                        
                        <a href="#" class="flex items-center text-gray-600 hover:text-indigo-600">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>FAQ Seputar Hotel</span>
                        </a>
                        
                        <a href="#" class="flex items-center text-gray-600 hover:text-indigo-600">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span>Email Customer Service</span>
                        </a>
                    </div>
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
        // Initialize map if coordinates are available
        @if(isset($hotel->latitude) && isset($hotel->longitude))
            const map = L.map('map').setView([{{ $hotel->latitude }}, {{ $hotel->longitude }}], 15);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            L.marker([{{ $hotel->latitude }}, {{ $hotel->longitude }}])
                .addTo(map)
                .bindPopup('{{ $hotel->name }}')
                .openPopup();
        @endif
        
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
        const checkInDate = document.getElementById('check_in_date');
        const checkOutDate = document.getElementById('check_out_date');
        const numberOfRooms = document.getElementById('number_of_rooms');
        const totalPriceElement = document.getElementById('total-price');
        
        if (checkInDate && checkOutDate && numberOfRooms && totalPriceElement) {
            const pricePerNight = {{ $hotel->price_per_night }};
            
            function calculateTotal() {
                if (checkInDate.value && checkOutDate.value) {
                    const startDate = new Date(checkInDate.value);
                    const endDate = new Date(checkOutDate.value);
                    
                    if (startDate && endDate && endDate > startDate) {
                        const diffTime = Math.abs(endDate - startDate);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                        const rooms = parseInt(numberOfRooms.value) || 1;
                        
                        const total = pricePerNight * diffDays * rooms;
                        totalPriceElement.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
                    }
                }
            }
            
            checkInDate.addEventListener('change', calculateTotal);
            checkOutDate.addEventListener('change', calculateTotal);
            numberOfRooms.addEventListener('change', calculateTotal);
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
                    const delay = element.getAttribute('data-delay') || '0';
                    
                    element.style.animationDelay = delay;
                    element.classList.add(animation);
                    element.style.opacity = '1';
                    
                    observer.unobserve(element);
                }
            });
        }, { threshold: 0.1 });
        
        animatedElements.forEach(element => {
            observer.observe(element);
        });
        
        // Smooth scrolling for anchor links
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
        const loadMoreBtn = document.getElementById('load-more-reviews');
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', function() {
                // In a real implementation, this would load more reviews via AJAX
                // For now, let's just redirect to a full reviews page
                window.location.href = "{{ route('hotels.show', $hotel->id) }}?reviews=all";
            });
        }
    });
</script>
@endpush