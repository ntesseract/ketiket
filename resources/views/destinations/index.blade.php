@extends('layouts.app')

@section('title', 'Destinasi Wisata')

@push('styles')
<style>
    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    .fade-in {
        animation: fadeIn 0.6s ease-in-out forwards;
    }
    
    .slide-up {
        animation: slideUp 0.6s ease-in-out forwards;
    }
    
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    
    /* Hover Effects */
    .card-hover {
        transition: all 0.3s ease;
    }
    
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    /* Image Zoom Effect */
    .img-zoom {
        overflow: hidden;
    }
    
    .img-zoom img {
        transition: transform 0.5s ease;
    }
    
    .img-zoom:hover img {
        transform: scale(1.05);
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<div class="relative h-96 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/destinations-hero.jpg') }}" alt="Destinasi Wisata" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gray-900 opacity-60"></div>
    </div>
    <div class="container mx-auto px-4 h-full flex items-center justify-center relative z-10">
        <div class="text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 slide-up">Jelajahi Destinasi Wisata</h1>
            <p class="text-xl md:text-2xl max-w-2xl mx-auto slide-up delay-100">Temukan tempat-tempat menarik untuk dikunjungi di seluruh Indonesia</p>
            <div class="mt-8 slide-up delay-200">
                <a href="#destinations" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md transition-colors duration-300">
                    Lihat Semua Destinasi
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
                <a href="{{ route('destinations.recommended') }}" class="ml-4 inline-flex items-center px-6 py-3 bg-white hover:bg-gray-100 text-indigo-600 font-medium rounded-md transition-colors duration-300">
                    <svg class="mr-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Rekomendasi Untukmu
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Section -->
<div class="py-8 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('destinations.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search Input -->
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Destinasi</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Nama atau lokasi destinasi">
                    </div>
                </div>
                
                <!-- Price Range -->
                <div>
                    <label for="min_price" class="block text-sm font-medium text-gray-700 mb-1">Harga Min</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="min_price" id="min_price" value="{{ request('min_price') }}" class="pl-12 focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-md sm:text-sm border-gray-300" placeholder="10000">
                    </div>
                </div>
                
                <div>
                    <label for="max_price" class="block text-sm font-medium text-gray-700 mb-1">Harga Max</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="max_price" id="max_price" value="{{ request('max_price') }}" class="pl-12 focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-md sm:text-sm border-gray-300" placeholder="100000">
                    </div>
                </div>
                
                <!-- Sorting -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Urutkan</label>
                    <div class="flex gap-2">
                        <select name="sort_by" class="block w-full bg-white border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama</option>
                            <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Harga</option>
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                        </select>
                        <select name="sort_order" class="block w-full bg-white border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Naik</option>
                            <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Turun</option>
                        </select>
                    </div>
                </div>
                
                <!-- Buttons -->
                <div class="md:col-span-2 flex items-end gap-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-300 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('destinations.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md transition-colors duration-300 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Destination Listing -->
<div id="destinations" class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Destinasi Wisata</h2>
            <p class="text-gray-600">Menampilkan {{ $destinations->total() }} destinasi wisata</p>
        </div>
        
        @if($destinations->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($destinations as $destination)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden card-hover opacity-0" data-animate="fade-in" data-delay="{{ $loop->index % 4 * 100 }}">
                        <div class="relative img-zoom h-48">
                            @if($destination->image)
                                <img src="{{ Storage::url($destination->image) }}" alt="{{ $destination->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                                    <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute top-0 right-0 m-2">
                                <span class="px-2 py-1 text-xs font-semibold bg-white/90 text-indigo-600 rounded-md">
                                    Rp {{ number_format($destination->price, 0, ',', '.') }}
                                </span>
                            </div>
                            
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3">
                                <h3 class="text-white font-bold text-lg truncate">{{ $destination->name }}</h3>
                                <p class="text-white/90 text-sm flex items-center">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="truncate">{{ $destination->location }}</span>
                                </p>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <div class="flex justify-between items-center mb-3">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span class="ml-1 text-sm text-gray-600">{{ number_format($destination->averageRating(), 1) }} ({{ $destination->reviews->count() }})</span>
                                </div>
                                <div class="text-gray-500 text-sm">
                                    <span class="inline-flex items-center text-{{ $destination->status === 'open' ? 'green' : ($destination->status === 'maintenance' ? 'yellow' : 'red') }}-600">
                                        <svg class="h-2 w-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        {{ ucfirst($destination->status) }}
                                    </span>
                                </div>
                            </div>
                            
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit($destination->description, 100) }}</p>
                            
                            <div class="flex space-x-2">
                                <a href="{{ route('destinations.show', $destination) }}" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-center py-2 px-4 rounded-md transition-colors duration-300">
                                    Detail
                                </a>
                                <a href="{{ route('booking.createDestination', $destination) }}" class="w-full bg-green-600 hover:bg-green-700 text-white text-center py-2 px-4 rounded-md transition-colors duration-300">
                                    Booking
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $destinations->appends(request()->except('page'))->links() }}
            </div>
        @else
            <div class="bg-white p-10 rounded-lg shadow-md text-center">
                <svg class="h-20 w-20 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Tidak Ada Destinasi</h3>
                <p class="text-gray-600 mb-4">Maaf, tidak ada destinasi yang cocok dengan pencarian Anda.</p>
                <a href="{{ route('destinations.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700">
                    Reset Pencarian
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Call to Action -->
<div class="bg-indigo-700 py-12">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Butuh rekomendasi wisata?</h2>
        <p class="text-xl text-indigo-100 mb-8 max-w-3xl mx-auto">Tanya langsung ke asisten AI kami untuk mendapatkan rekomendasi destinasi yang sesuai dengan preferensi Anda</p>
        <a href="{{ route('chat.index') }}" class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-100 text-indigo-600 font-medium rounded-md transition-colors duration-300">
            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
            </svg>
            Chat dengan AI Travel Assistant
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation on scroll
        const animatedElements = document.querySelectorAll('[data-animate]');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    const animation = element.getAttribute('data-animate');
                    const delay = element.getAttribute('data-delay') || 0;
                    
                    setTimeout(() => {
                        element.classList.add(animation);
                    }, delay);
                    
                    observer.unobserve(element);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        animatedElements.forEach(element => {
            observer.observe(element);
        });
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection