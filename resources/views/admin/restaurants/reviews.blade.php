@extends('layouts.admin')

@section('title', 'Ulasan Restoran')

@section('header', 'Ulasan Restoran: ' . $restaurant->name)

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
    
    .fade-in {
        opacity: 0;
        animation: fadeIn 0.5s ease-in-out forwards;
    }
    
    .slide-up {
        opacity: 0;
        animation: slideInUp 0.5s ease-in-out forwards;
    }
    
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    
    /* Hover Effect */
    .hover-scale {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-scale:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<div id="loading-overlay" class="fixed inset-0 bg-white bg-opacity-90 z-50 flex items-center justify-center">
    <div class="text-center">
        <div class="w-12 h-12 border-4 border-t-blue-500 border-blue-200 rounded-full animate-spin mb-4"></div>
        <p class="text-gray-600">Memuat ulasan...</p>
    </div>
</div>

<div class="opacity-0" id="main-content">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6 hover-scale">
        <div class="flex items-center p-6">
            <div class="flex-shrink-0 mr-4">
                @if($restaurant->image_path)
                    <img src="{{ Storage::url($restaurant->image_path) }}" alt="{{ $restaurant->name }}" class="h-24 w-24 object-cover rounded-lg">
                @else
                    <div class="h-24 w-24 rounded-lg bg-gradient-to-r from-orange-500 to-red-600 flex items-center justify-center">
                        <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z"></path>
                        </svg>
                    </div>
                @endif
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $restaurant->name }}</h1>
                <p class="text-gray-600">
                    <svg class="h-4 w-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    {{ $restaurant->location }}
                </p>
                @if($restaurant->cuisine_type)
                <p class="text-gray-600">
                    <svg class="h-4 w-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ $restaurant->cuisine_type }}
                </p>
                @endif
            </div>
            
            <div class="ml-auto">
                <a href="{{ route('admin.restaurants.show', $restaurant) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Detail
                </a>
            </div>
        </div>
    </div>
    
    <!-- Rating Stats -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6 hover-scale opacity-0" data-animate="fade-in">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="h-5 w-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Statistik Rating
        </h2>
        
        <div class="flex flex-col md:flex-row md:items-center md:space-x-12">
            <div class="text-center mb-6 md:mb-0">
                @php
                    $averageRating = $reviews->count() > 0 ? $reviews->avg('rating') : 0;
                @endphp
                <div class="text-6xl font-bold text-gray-800">{{ number_format($averageRating, 1) }}</div>
                <div class="flex justify-center items-center mt-2">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($averageRating))
                            <svg class="h-6 w-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @else
                            <svg class="h-6 w-6 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @endif
                    @endfor
                </div>
                <div class="text-sm text-gray-500 mt-1">{{ $reviews->count() }} ulasan</div>
            </div>
            
            <div class="flex-1">
                @for($i = 5; $i >= 1; $i--)
                    @php
                        $reviewCount = $reviews->where('rating', $i)->count();
                        $percentage = $reviews->count() > 0 ? ($reviewCount / $reviews->count()) * 100 : 0;
                    @endphp
                    <div class="flex items-center mb-2">
                        <span class="w-3 text-sm font-medium text-gray-700">{{ $i }}</span>
                        <svg class="h-5 w-5 text-yellow-400 ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <div class="flex-1 h-3 mx-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-yellow-400 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                        <span class="text-sm text-gray-600 w-8">{{ $reviewCount }}</span>
                        <span class="text-sm text-gray-500 w-12">({{ number_format($percentage, 1) }}%)</span>
                    </div>
                @endfor
            </div>
        </div>
    </div>
    
    <!-- Reviews List -->
    <div class="bg-white rounded-lg shadow-md p-6 hover-scale opacity-0" data-animate="fade-in" data-delay="200">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="h-5 w-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
            </svg>
            Semua Ulasan
        </h2>
        
        <!-- Filters -->
        <div class="mb-6 flex flex-wrap gap-2">
            <form method="GET" action="{{ route('admin.restaurants.manage-reviews', $restaurant) }}" class="flex flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <label for="rating_filter" class="text-sm text-gray-700">Rating:</label>
                    <select name="rating" id="rating_filter" class="rounded-md border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500" onchange="this.form.submit()">
                        <option value="">Semua Rating</option>
                        @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                            {{ $i }} Bintang
                        </option>
                        @endfor
                    </select>
                </div>
                
                <div class="flex items-center gap-2">
                    <label for="sort_by" class="text-sm text-gray-700">Urutkan:</label>
                    <select name="sort_by" id="sort_by" class="rounded-md border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500" onchange="this.form.submit()">
                        <option value="created_at" {{ request('sort_by') == 'created_at' || !request('sort_by') ? 'selected' : '' }}>Tanggal</option>
                        <option value="rating" {{ request('sort_by') == 'rating' ? 'selected' : '' }}>Rating</option>
                    </select>
                </div>
                
                <div class="flex items-center gap-2">
                    <label for="sort_order" class="text-sm text-gray-700">Arah:</label>
                    <select name="sort_order" id="sort_order" class="rounded-md border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500" onchange="this.form.submit()">
                        <option value="desc" {{ request('sort_order') == 'desc' || !request('sort_order') ? 'selected' : '' }}>Menurun</option>
                        <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Menaik</option>
                    </select>
                </div>
                
                @if(request('rating') || request('sort_by') || request('sort_order'))
                <a href="{{ route('admin.restaurants.manage-reviews', $restaurant) }}" class="text-xs text-indigo-600 hover:text-indigo-800 self-center ml-2">
                    <svg class="h-4 w-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Reset Filter
                </a>
                @endif
            </form>
        </div>
        
        @if($reviews->count() > 0)
            <div class="space-y-6">
                @foreach($reviews as $review)
                <div class="p-5 border border-gray-200 rounded-lg hover-scale transition-all duration-300 opacity-0" data-animate="slide-up" data-delay="{{ $loop->index * 100 }}">
                    <div class="flex justify-between">
                        <div class="flex items-center mb-2">
                            <div class="w-10 h-10 rounded-full bg-indigo-500 text-white flex items-center justify-center font-semibold mr-3">
                                {{ strtoupper(substr($review->user->name ?? 'User', 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-800">{{ $review->user->name ?? 'Pengguna' }}</div>
                                <div class="text-xs text-gray-500">{{ $review->created_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        <div class="flex">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
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
                    </div>
                    <div class="mt-3 text-gray-700">{{ $review->comment }}</div>
                    
                    <!-- Action Buttons -->
                    <div class="mt-4 flex justify-end space-x-2">
                        <form method="POST" action="{{ route('admin.restaurants.delete-review', [$restaurant, $review->id]) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ulasan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                <svg class="h-4 w-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-6">
                {{ $reviews->links() }}
            </div>
        @else
            <div class="py-8 text-center text-gray-500">
                <svg class="h-16 w-16 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                </svg>
                <p class="text-lg">Belum ada ulasan untuk restoran ini</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
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
            
            // Animate elements with slide-up
            const slideElements = document.querySelectorAll('[data-animate="slide-up"]');
            slideElements.forEach((element) => {
                setTimeout(() => {
                    element.classList.add('slide-up');
                }, element.getAttribute('data-delay') || 0);
            });
        }, 800);
        
        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId !== '#') {
                    document.querySelector(targetId).scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection
