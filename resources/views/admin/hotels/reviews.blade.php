@extends('layouts.admin')

@section('title', 'Kelola Review Hotel')

@section('content')
<div class="space-y-6">
    <!-- Hotel Info Header -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0 mr-4">
                    @if ($hotel->image)
                        <img src="{{ Storage::url($hotel->image) }}" alt="{{ $hotel->name }}" class="h-16 w-16 object-cover rounded-lg">
                    @else
                        <div class="h-16 w-16 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-hotel text-indigo-400 text-2xl"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-1">{{ $hotel->name }}</h1>
                    <div class="flex items-center text-sm text-gray-600">
                        <div class="flex items-center mr-4">
                            <i class="fas fa-map-marker-alt mr-1 text-indigo-500"></i>
                            <span>{{ $hotel->location }}</span>
                        </div>
                        <div class="flex items-center">
                            <div class="flex">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $hotel->star_rating)
                                        <i class="fas fa-star text-yellow-400"></i>
                                    @else
                                        <i class="far fa-star text-yellow-400"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="ml-1">{{ $hotel->star_rating }} Bintang</span>
                        </div>
                    </div>
                </div>
                <div class="ml-auto">
                    <a href="{{ route('admin.hotels.show', $hotel->id) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Detail Hotel
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Review Stats -->
        <div class="p-6 bg-indigo-50">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg p-4 shadow-sm transform transition-transform duration-200 hover:scale-105 hover:shadow-md">
                    <p class="text-sm text-gray-500 font-medium">Rating Rata-rata</p>
                    <div class="flex items-center mt-2">
                        <span class="text-2xl font-bold text-indigo-600 mr-2">{{ number_format($hotel->averageRating(), 1) }}</span>
                        <div class="flex">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($hotel->averageRating()))
                                    <i class="fas fa-star text-yellow-400"></i>
                                @else
                                    <i class="far fa-star text-yellow-400"></i>
                                @endif
                            @endfor
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg p-4 shadow-sm transform transition-transform duration-200 hover:scale-105 hover:shadow-md">
                    <p class="text-sm text-gray-500 font-medium">Total Review</p>
                    <p class="text-2xl font-bold text-indigo-600 mt-2">{{ $reviews->total() }}</p>
                </div>
                
                <div class="bg-white rounded-lg p-4 shadow-sm transform transition-transform duration-200 hover:scale-105 hover:shadow-md">
                    <p class="text-sm text-gray-500 font-medium">Review Terbaru</p>
                    <p class="text-2xl font-bold text-indigo-600 mt-2">
                        @if($reviews->count() > 0)
                            {{ $reviews->first()->created_at->diffForHumans() }}
                        @else
                            -
                        @endif
                    </p>
                </div>
                
                <div class="bg-white rounded-lg p-4 shadow-sm transform transition-transform duration-200 hover:scale-105 hover:shadow-md">
                    <p class="text-sm text-gray-500 font-medium">Rating Breakdown</p>
                    <div class="flex space-x-2 mt-2">
                        @php
                            $ratings = $reviews->groupBy('rating');
                            $totalReviews = $reviews->count();
                            $colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-lime-500', 'bg-green-500'];
                        @endphp
                        
                        @for($i = 5; $i >= 1; $i--)
                            @php
                                $count = isset($ratings[$i]) ? $ratings[$i]->count() : 0;
                                $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                            @endphp
                            <div class="w-1/5 h-10 flex flex-col items-center space-y-1">
                                <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                                    <div class="{{ $colors[$i-1] }} h-full rounded-full transition-all duration-1000 ease-out" style="width: 0%" data-percentage="{{ $percentage }}"></div>
                                </div>
                                <span class="text-xs text-gray-600">{{ $i }}★</span>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Review Filter & Search -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden slide-in-bottom" data-animate="slide-in-bottom">
        <div class="p-6">
            <form action="{{ route('admin.hotels.reviews', $hotel->id) }}" method="GET" class="flex flex-col md:flex-row md:items-end space-y-4 md:space-y-0 md:space-x-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        placeholder="Cari berdasarkan kata kunci...">
                </div>
                
                <div>
                    <label for="rating" class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                    <select name="rating" id="rating" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">Semua Rating</option>
                        @for ($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Bintang</option>
                        @endfor
                    </select>
                </div>
                
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                    <select name="sort" id="sort" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="highest" {{ request('sort') == 'highest' ? 'selected' : '' }}>Rating Tertinggi</option>
                        <option value="lowest" {{ request('sort') == 'lowest' ? 'selected' : '' }}>Rating Terendah</option>
                    </select>
                </div>
                
                <div class="flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    
                    <a href="{{ route('admin.hotels.reviews', $hotel->id) }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        <i class="fas fa-sync-alt mr-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Reviews List -->
    <div class="space-y-4">
        @if($reviews->count() > 0)
            @foreach($reviews as $index => $review)
                <div class="bg-white shadow-md rounded-lg overflow-hidden slide-in-left" data-animate="slide-in-left" style="animation-delay: {{ $index * 100 }}ms;">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between">
                            <div class="flex items-start">
                                <div class="mr-4">
                                    @if($review->user->profile_picture)
                                        <img src="{{ Storage::url($review->user->profile_picture) }}" alt="{{ $review->user->name }}" class="h-12 w-12 rounded-full object-cover">
                                    @else
                                        <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-indigo-800 font-semibold">{{ strtoupper(substr($review->user->name, 0, 1)) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">{{ $review->user->name }}</h3>
                                    <div class="flex items-center mt-1">
                                        <div class="flex">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <i class="fas fa-star text-yellow-400"></i>
                                                @else
                                                    <i class="far fa-star text-yellow-400"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="ml-2 text-sm text-gray-500">{{ $review->created_at->format('d M Y, H:i') }}</span>
                                    </div>
                                    <div class="mt-3 text-gray-600">
                                        {{ $review->comment }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 md:mt-0 flex space-x-3">
                                <form action="{{ route('admin.reviews.delete', $review->id) }}" method="POST" class="inline-block delete-review-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 flex items-center">
                                        <i class="fas fa-trash-alt mr-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
            <!-- Pagination -->
            <div class="mt-6">
                {{ $reviews->withQueryString()->links() }}
            </div>
        @else
            <div class="bg-white shadow-md rounded-lg overflow-hidden py-8">
                <div class="text-center">
                    <div class="text-gray-400 mb-3">
                        <i class="far fa-comment-alt text-5xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak Ada Review</h3>
                    <p class="text-gray-500">Belum ada review untuk hotel ini</p>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate rating bars
        const ratingBars = document.querySelectorAll('[data-percentage]');
        
        function animateRatingBars() {
            ratingBars.forEach(bar => {
                const percentage = parseFloat(bar.getAttribute('data-percentage'));
                setTimeout(() => {
                    bar.style.width = percentage + '%';
                }, 300);
            });
        }
        
        // Trigger animation
        animateRatingBars();
        
        // Confirm delete review
        const deleteForms = document.querySelectorAll('.delete-review-form');
        
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (confirm('Apakah Anda yakin ingin menghapus review ini? Tindakan ini tidak dapat dibatalkan.')) {
                    this.submit();
                }
            });
        });
        
        // Animated scroll to top when changing page
        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                
                // Smooth scroll to top before changing page
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                
                // Change page after scroll
                setTimeout(() => {
                    window.location.href = url;
                }, 500);
            });
        });
    });
</script>
@endpush
@endsection