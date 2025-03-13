@extends('layouts.admin')

@section('title', 'Kelola Paket Wisata')

@section('content')
<div class="space-y-6">
    <!-- Header & Actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Paket Wisata</h1>
            <p class="mt-1 text-sm text-gray-600">
                Kelola semua paket wisata dan pengaturannya
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.packages.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i> Tambah Paket
            </a>
        </div>
    </div>
    
    <!-- Search & Filters -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <form action="{{ route('admin.packages.index') }}" method="GET" class="flex flex-col md:flex-row md:items-end space-y-4 md:space-y-0 md:space-x-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        placeholder="Cari berdasarkan nama paket...">
                </div>
                
                <div>
                    <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Durasi</label>
                    <select name="duration" id="duration" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">Semua Durasi</option>
                        @foreach($durations as $duration)
                            <option value="{{ $duration }}" {{ request('duration') == $duration ? 'selected' : '' }}>{{ $duration }} Hari</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="min_price" class="block text-sm font-medium text-gray-700 mb-1">Harga Minimum</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="min_price" id="min_price" value="{{ request('min_price') }}"
                            class="w-full pl-12 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            placeholder="0">
                    </div>
                </div>
                
                <div>
                    <label for="max_price" class="block text-sm font-medium text-gray-700 mb-1">Harga Maksimum</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="max_price" id="max_price" value="{{ request('max_price') }}"
                            class="w-full pl-12 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            placeholder="10000000">
                    </div>
                </div>
                
                <div>
                    <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                    <select name="sort_by" id="sort_by" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="name" {{ request('sort_by', 'name') == 'name' ? 'selected' : '' }}>Nama (A-Z)</option>
                        <option value="name_desc" {{ request('sort_by') == 'name_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                        <option value="price_asc" {{ request('sort_by') == 'price_asc' ? 'selected' : '' }}>Harga (Termurah)</option>
                        <option value="price_desc" {{ request('sort_by') == 'price_desc' ? 'selected' : '' }}>Harga (Termahal)</option>
                        <option value="duration_asc" {{ request('sort_by') == 'duration_asc' ? 'selected' : '' }}>Durasi (Terpendek)</option>
                        <option value="duration_desc" {{ request('sort_by') == 'duration_desc' ? 'selected' : '' }}>Durasi (Terpanjang)</option>
                        <option value="newest" {{ request('sort_by') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort_by') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                    </select>
                </div>
                
                <div class="flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    
                    <a href="{{ route('admin.packages.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        <i class="fas fa-sync-alt mr-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Packages Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($packages as $package)
            <div class="bg-white shadow-md rounded-lg overflow-hidden transition duration-300 transform hover:scale-105 hover:shadow-lg">
                <div class="relative h-48 bg-indigo-700 overflow-hidden">
                    @if ($package->image)
                        <img src="{{ Storage::url($package->image) }}" alt="{{ $package->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-indigo-100">
                            <i class="fas fa-suitcase text-indigo-300 text-6xl"></i>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black opacity-70"></div>
                    <div class="absolute bottom-0 left-0 p-4 text-white">
                        <h2 class="text-xl font-bold">{{ $package->name }}</h2>
                        <div class="flex items-center mt-1 text-sm">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            <span>{{ $package->duration_days }} Hari</span>
                        </div>
                    </div>
                </div>
                
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-bold text-lg text-indigo-600">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                        <div class="flex items-center">
                            <i class="fas fa-star text-yellow-400 mr-1"></i>
                            <span>{{ number_format($package->getAverageRating(), 1) }}</span>
                        </div>
                    </div>
                    
                    <p class="text-gray-600 text-sm mb-4">{{ Str::limit($package->description, 100) }}</p>
                    
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <div class="text-center bg-blue-50 p-2 rounded">
                            <div class="font-bold text-blue-600">{{ $package->destinations->count() }}</div>
                            <div class="text-xs text-gray-500">Destinasi</div>
                        </div>
                        <div class="text-center bg-purple-50 p-2 rounded">
                            <div class="font-bold text-purple-600">{{ $package->hotels->count() }}</div>
                            <div class="text-xs text-gray-500">Hotel</div>
                        </div>
                        <div class="text-center bg-green-50 p-2 rounded">
                            <div class="font-bold text-green-600">{{ $package->restaurants->count() }}</div>
                            <div class="text-xs text-gray-500">Restoran</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                        <a href="{{ route('admin.packages.show', $package->id) }}" class="text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-eye mr-1"></i> Detail
                        </a>
                        <div class="flex space-x-1">
                            <a href="{{ route('admin.packages.edit', $package->id) }}" class="p-1.5 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('admin.packages.itinerary', $package->id) }}" class="p-1.5 bg-purple-500 text-white rounded hover:bg-purple-600">
                                <i class="fas fa-route"></i>
                            </a>
                            <form action="{{ route('admin.packages.destroy', $package->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus paket ini?');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 bg-red-500 text-white rounded hover:bg-red-600">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-1 md:col-span-2 lg:col-span-3 bg-white shadow-md rounded-lg p-8 text-center">
                <div class="text-gray-400 mb-3">
                    <i class="fas fa-suitcase text-6xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak Ada Paket Wisata</h3>
                <p class="text-gray-500 mb-4">Belum ada paket wisata yang tersedia</p>
                <a href="{{ route('admin.packages.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i> Tambah Paket Baru
                </a>
            </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    <div class="bg-white shadow-md rounded-lg p-4">
        {{ $packages->withQueryString()->links() }}
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add fade-in animation to the cards
        const cards = document.querySelectorAll('.grid > div');
        cards.forEach((card, index) => {
            card.style.opacity = 0;
            card.style.transform = 'translateY(10px)';
            
            setTimeout(() => {
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                card.style.opacity = 1;
                card.style.transform = 'translateY(0)';
            }, 100 * index);
        });
        
        // Add smooth scroll animation when changing pages
        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                
                document.querySelectorAll('.grid > div').forEach((card, index) => {
                    setTimeout(() => {
                        card.style.opacity = 0;
                        card.style.transform = 'translateY(10px)';
                    }, 50 * index);
                });
                
                setTimeout(() => {
                    window.location.href = url;
                }, 500);
            });
        });
    });
</script>
@endpush
@endsection